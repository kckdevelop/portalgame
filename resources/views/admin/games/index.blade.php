@extends('layouts.admin')

@section('title', 'Daftar & Kelola Game')

@section('content')
<div class="space-y-6">

    <!-- Top Action & Search Bar -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-slate-900 border border-slate-800 rounded-2xl p-4">
        
        <form action="{{ route('admin.games.index') }}" method="GET" class="flex flex-1 items-center space-x-3 w-full sm:w-auto">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul game..."
                    class="w-full bg-slate-950 border border-slate-700/80 rounded-xl py-2 pl-10 pr-4 text-xs text-slate-200 focus:outline-none focus:border-purple-500">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            </div>
            
            <select name="category" onchange="this.form.submit()"
                class="bg-slate-950 border border-slate-700/80 rounded-xl py-2 px-3 text-xs text-slate-200 focus:outline-none focus:border-purple-500">
                <option value="">Semua Kategori</option>
                @foreach(['Action', 'Arcade', 'Puzzle', 'Racing', 'Sports', 'Adventure', 'Strategy', 'Casual'] as $cat)
                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
        </form>

        <a href="{{ route('admin.games.create') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-medium text-xs shadow-md shadow-purple-600/30 transition-all flex items-center justify-center space-x-2">
            <i class="fa-solid fa-plus"></i>
            <span>Upload Game (ZIP)</span>
        </a>

    </div>

    <!-- Games Data Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 text-slate-400 font-semibold uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Game &amp; Thumbnail</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Lokasi Folder &amp; Entry</th>
                        <th class="px-6 py-4">Statistik</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($games as $game)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            
                            <!-- Game Info -->
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ $game->thumbnail_url }}" alt="{{ $game->title }}" class="w-14 h-14 rounded-xl object-cover bg-slate-950 border border-slate-800">
                                    <div class="space-y-0.5">
                                        <h4 class="font-outfit font-bold text-sm text-white">{{ $game->title }}</h4>
                                        <p class="text-[11px] text-slate-400 font-mono">/game/{{ $game->slug }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Category -->
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-lg bg-purple-500/10 border border-purple-500/30 text-purple-300 font-medium">
                                    {{ $game->category }}
                                </span>
                            </td>

                            <!-- Folder Name & Entry Point -->
                            <td class="px-6 py-4">
                                <div class="font-mono text-[11px] space-y-0.5">
                                    <span class="block text-slate-300"><i class="fa-solid fa-folder text-amber-400 mr-1"></i> {{ $game->folder_name }}</span>
                                    <span class="block text-slate-500"><i class="fa-solid fa-file-code text-cyan-400 mr-1"></i> {{ $game->entry_file }}</span>
                                </div>
                            </td>

                            <!-- Stats -->
                            <td class="px-6 py-4">
                                <div class="space-y-0.5 text-[11px]">
                                    <span class="block text-slate-300"><i class="fa-solid fa-circle-play text-cyan-400 mr-1"></i> {{ number_format($game->plays_count) }} plays</span>
                                    <span class="block text-slate-400"><i class="fa-solid fa-eye text-purple-400 mr-1"></i> {{ number_format($game->views_count) }} views</span>
                                </div>
                            </td>

                            <!-- Active Toggle -->
                            <td class="px-6 py-4">
                                <form action="{{ route('admin.games.toggle', $game) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-[11px] font-semibold transition-all {{ $game->is_active ? 'bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 hover:bg-emerald-500/20' : 'bg-slate-800 border border-slate-700 text-slate-400 hover:bg-slate-700' }}">
                                        <i class="fa-solid {{ $game->is_active ? 'fa-circle-check text-emerald-400' : 'fa-circle-xmark text-slate-500' }}"></i>
                                        <span>{{ $game->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                    </button>
                                </form>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    
                                    <!-- Test Play -->
                                    <a href="{{ route('portal.play', $game->slug) }}" target="_blank" title="Uji Game di Tab Baru"
                                        class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-purple-600 text-slate-300 hover:text-white flex items-center justify-center transition-colors">
                                        <i class="fa-solid fa-play text-xs"></i>
                                    </a>

                                    <!-- Edit -->
                                    <a href="{{ route('admin.games.edit', $game) }}" title="Edit Info Game"
                                        class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-amber-600 text-slate-300 hover:text-white flex items-center justify-center transition-colors">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </a>

                                    <!-- Delete -->
                                    <form action="{{ route('admin.games.destroy', $game) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus game ini beserta seluruh folder ekstraknya?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus Game"
                                            class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-rose-600 text-slate-300 hover:text-white flex items-center justify-center transition-colors">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                Belum ada game terdaftar. Klik tombol <strong class="text-purple-400">Upload Game (ZIP)</strong> di atas untuk menambahkan game HTML5 pertama Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($games->hasPages())
            <div class="p-4 border-t border-slate-800">
                {{ $games->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
