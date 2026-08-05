<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Services\GameExtractorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminGameController extends Controller
{
    protected GameExtractorService $extractor;

    public function __construct(GameExtractorService $extractor)
    {
        $this->extractor = $extractor;
    }

    /**
     * Dashboard statistics view
     */
    public function dashboard()
    {
        $totalGames = Game::count();
        $activeGames = Game::where('is_active', true)->count();
        $totalPlays = Game::sum('plays_count');
        $totalViews = Game::sum('views_count');

        $latestGames = Game::latest()->limit(5)->get();
        $topPlayedGames = Game::orderBy('plays_count', 'desc')->limit(5)->get();

        return view('admin.dashboard', compact(
            'totalGames',
            'activeGames',
            'totalPlays',
            'totalViews',
            'latestGames',
            'topPlayedGames'
        ));
    }

    /**
     * List all games in admin panel
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $category = $request->query('category');

        $query = Game::query();

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($category && $category !== 'All') {
            $query->where('category', $category);
        }

        $games = $query->latest()->paginate(10)->withQueryString();

        return view('admin.games.index', compact('games', 'search', 'category'));
    }

    /**
     * Show game creation form
     */
    public function create()
    {
        $categories = ['Action', 'Arcade', 'Puzzle', 'Racing', 'Sports', 'Adventure', 'Strategy', 'Casual'];
        return view('admin.games.create', compact('categories'));
    }

    /**
     * Store uploaded ZIP game and metadata
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:5120', // Max 5MB
            'game_zip' => 'required|file|mimes:zip|max:102400', // Max 100MB
            'entry_file' => 'nullable|string|max:100',
        ], [
            'title.required' => 'Judul game wajib diisi.',
            'category.required' => 'Kategori game wajib dipilih.',
            'thumbnail.required' => 'Gambar thumbnail wajib diunggah.',
            'thumbnail.image' => 'Thumbnail harus berformat gambar valid.',
            'game_zip.required' => 'File ZIP project game wajib diunggah.',
            'game_zip.mimes' => 'File game harus berformat .zip',
            'game_zip.max' => 'Ukuran file ZIP maksimal 100MB.',
        ]);

        $slug = Str::slug($request->title);
        // Ensure unique slug
        $originalSlug = $slug;
        $count = 1;
        while (Game::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        $folderName = $slug;

        // 1. Upload Thumbnail Image
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        // 2. Extract ZIP archive
        try {
            $customEntry = $request->input('entry_file') ?: 'index.html';
            $extractResult = $this->extractor->extractGameArchive(
                $request->file('game_zip'),
                $folderName,
                $customEntry
            );

            $game = Game::create([
                'title' => $request->title,
                'slug' => $slug,
                'description' => $request->description,
                'category' => $request->category,
                'thumbnail' => $thumbnailPath,
                'folder_name' => $folderName,
                'entry_file' => $extractResult['entry_file'],
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('admin.games.index')
                ->with('success', "Game \"{$game->title}\" berhasil diunggah dan diekstrak!");

        } catch (\Exception $e) {
            // Clean up thumbnail if zip extraction fails
            if ($thumbnailPath && Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }

            return back()->withInput()->withErrors([
                'game_zip' => 'Gagal menguraikan file ZIP: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Edit game metadata
     */
    public function edit(Game $game)
    {
        $categories = ['Action', 'Arcade', 'Puzzle', 'Racing', 'Sports', 'Adventure', 'Strategy', 'Casual'];
        return view('admin.games.edit', compact('game', 'categories'));
    }

    /**
     * Update game metadata & optional ZIP/thumbnail replacement
     */
    public function update(Request $request, Game $game)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'game_zip' => 'nullable|file|mimes:zip|max:102400',
            'entry_file' => 'nullable|string|max:100',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'is_active' => $request->has('is_active'),
            'entry_file' => $request->input('entry_file') ?: $game->entry_file,
        ];

        // Replace thumbnail if provided
        if ($request->hasFile('thumbnail')) {
            if ($game->thumbnail && Storage::disk('public')->exists($game->thumbnail)) {
                Storage::disk('public')->delete($game->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        // Replace ZIP project if provided
        if ($request->hasFile('game_zip')) {
            try {
                $extractResult = $this->extractor->extractGameArchive(
                    $request->file('game_zip'),
                    $game->folder_name,
                    $request->input('entry_file') ?: $game->entry_file
                );
                $data['entry_file'] = $extractResult['entry_file'];
            } catch (\Exception $e) {
                return back()->withInput()->withErrors([
                    'game_zip' => 'Gagal memperbarui ZIP game: ' . $e->getMessage(),
                ]);
            }
        }

        $game->update($data);

        return redirect()->route('admin.games.index')
            ->with('success', "Game \"{$game->title}\" berhasil diperbarui.");
    }

    /**
     * Delete game and clean up files
     */
    public function destroy(Game $game)
    {
        $title = $game->title;

        // Delete extracted game folder
        $this->extractor->deleteGameDirectory($game->folder_name);

        // Delete thumbnail image
        if ($game->thumbnail && Storage::disk('public')->exists($game->thumbnail)) {
            Storage::disk('public')->delete($game->thumbnail);
        }

        $game->delete();

        return redirect()->route('admin.games.index')
            ->with('success', "Game \"{$title}\" beserta semua filenya berhasil dihapus.");
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(Game $game)
    {
        $game->is_active = !$game->is_active;
        $game->save();

        $statusStr = $game->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Status game \"{$game->title}\" berhasil {$statusStr}.");
    }
}
