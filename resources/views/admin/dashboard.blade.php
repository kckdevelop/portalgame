@extends('layouts.admin')

@section('title', 'Dashboard Overview')

@section('content')
<div class="space-y-8">

    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Total Games -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-400 font-medium">Total Game</span>
                <h3 class="font-outfit font-extrabold text-3xl text-white mt-1">{{ number_format($totalGames) }}</h3>
                <span class="text-[11px] text-emerald-400 font-semibold mt-1 inline-block">
                    <i class="fa-solid fa-circle-check mr-1"></i> {{ number_format($activeGames) }} Aktif
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-600/20 border border-purple-500/30 flex items-center justify-center text-purple-400 text-xl">
                <i class="fa-solid fa-gamepad"></i>
            </div>
        </div>

        <!-- Total Plays -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-400 font-medium">Total Dimainkan</span>
                <h3 class="font-outfit font-extrabold text-3xl text-cyan-400 mt-1">{{ number_format($totalPlays) }}</h3>
                <span class="text-[11px] text-slate-400 mt-1 inline-block">kali dimainkan pengunjung</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-cyan-600/20 border border-cyan-500/30 flex items-center justify-center text-cyan-400 text-xl">
                <i class="fa-solid fa-circle-play"></i>
            </div>
        </div>

        <!-- Total Views -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-400 font-medium">Total Dilihat</span>
                <h3 class="font-outfit font-extrabold text-3xl text-pink-400 mt-1">{{ number_format($totalViews) }}</h3>
                <span class="text-[11px] text-slate-400 mt-1 inline-block">tayangan detail game</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-pink-600/20 border border-pink-500/30 flex items-center justify-center text-pink-400 text-xl">
                <i class="fa-solid fa-eye"></i>
            </div>
        </div>

        <!-- Quick Upload Card -->
        <div class="bg-gradient-to-br from-purple-900/50 to-indigo-900/50 border border-purple-500/30 rounded-2xl p-6 flex flex-col justify-between">
            <div>
                <span class="text-xs text-purple-300 font-bold uppercase tracking-wider">Aksi Cepat</span>
                <h4 class="font-outfit font-bold text-lg text-white mt-1">Upload Project ZIP</h4>
            </div>
            <a href="{{ route('admin.games.create') }}" class="mt-4 w-full py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-semibold text-xs text-center shadow-lg shadow-purple-600/40 transition-all flex items-center justify-center space-x-2">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                <span>Unggah Game Baru</span>
            </a>
        </div>

    </div>

    <!-- Content Tables Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Latest Uploads -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-outfit font-bold text-lg text-white">Upload Terbaru</h3>
                <a href="{{ route('admin.games.index') }}" class="text-xs text-purple-400 hover:text-purple-300">Lihat Semua</a>
            </div>

            <div class="divide-y divide-slate-800/80">
                @forelse($latestGames as $game)
                    <div class="py-3 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <img src="{{ $game->thumbnail_url }}" alt="{{ $game->title }}" class="w-12 h-12 rounded-xl object-cover bg-slate-950">
                            <div>
                                <h4 class="font-outfit font-semibold text-sm text-white">{{ $game->title }}</h4>
                                <span class="text-[10px] text-purple-400 uppercase font-medium">{{ $game->category }}</span>
                            </div>
                        </div>
                        <div class="text-right text-xs">
                            <span class="block text-slate-300 font-mono">{{ $game->created_at->format('d/m/Y') }}</span>
                            <span class="text-[10px] {{ $game->is_active ? 'text-emerald-400' : 'text-slate-500' }}">
                                {{ $game->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 py-4 text-center">Belum ada game yang diunggah.</p>
                @endforelse
            </div>
        </div>

        <!-- Top Played Games -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-outfit font-bold text-lg text-white">Game Paling Banyak Dimainkan</h3>
                <a href="{{ route('admin.games.index') }}" class="text-xs text-purple-400 hover:text-purple-300">Lihat Semua</a>
            </div>

            <div class="divide-y divide-slate-800/80">
                @forelse($topPlayedGames as $game)
                    <div class="py-3 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <img src="{{ $game->thumbnail_url }}" alt="{{ $game->title }}" class="w-12 h-12 rounded-xl object-cover bg-slate-950">
                            <div>
                                <h4 class="font-outfit font-semibold text-sm text-white">{{ $game->title }}</h4>
                                <span class="text-[10px] text-cyan-400 font-medium">{{ number_format($game->plays_count) }}x Dimainkan</span>
                            </div>
                        </div>
                        <a href="{{ route('portal.play', $game->slug) }}" target="_blank" class="px-3 py-1 rounded-lg bg-slate-800 hover:bg-purple-600 text-slate-300 hover:text-white text-xs transition-colors">
                            <i class="fa-solid fa-play text-[10px] mr-1"></i> Uji Game
                        </a>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 py-4 text-center">Belum ada data permainan.</p>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
