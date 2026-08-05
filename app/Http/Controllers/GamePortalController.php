<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;

class GamePortalController extends Controller
{
    /**
     * Display portal homepage with hero, filter categories, search and game cards
     */
    public function index(Request $request)
    {
        $category = $request->query('category');
        $search = $request->query('search');

        $query = Game::where('is_active', true);

        if ($category && $category !== 'All') {
            $query->where('category', $category);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $games = $query->latest()->paginate(12)->withQueryString();

        // Featured game for hero section
        $featuredGame = Game::where('is_active', true)->orderBy('plays_count', 'desc')->first();

        // List of all categories with counts
        $categories = [
            'All',
            'Action',
            'Arcade',
            'Puzzle',
            'Racing',
            'Sports',
            'Adventure',
            'Strategy',
            'Casual',
        ];

        return view('portal.index', compact('games', 'featuredGame', 'categories', 'category', 'search'));
    }

    /**
     * Display game player page with responsive iframe theater mode
     */
    public function play(string $slug)
    {
        $game = Game::where('slug', $slug)->where('is_active', true)->firstOrFail();

        // Increment play count
        $game->incrementPlays();
        $game->incrementViews();

        // Related games
        $relatedGames = Game::where('is_active', true)
            ->where('id', '!=', $game->id)
            ->where('category', $game->category)
            ->limit(4)
            ->get();

        if ($relatedGames->count() < 4) {
            $moreGames = Game::where('is_active', true)
                ->where('id', '!=', $game->id)
                ->whereNotIn('id', $relatedGames->pluck('id'))
                ->limit(4 - $relatedGames->count())
                ->get();
            $relatedGames = $relatedGames->merge($moreGames);
        }

        return view('portal.play', compact('game', 'relatedGames'));
    }
}
