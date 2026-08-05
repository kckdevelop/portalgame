@extends('layouts.app')

@section('title', 'Portal Game Musaba - SMK Muhammadiyah 1 Bantul')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-12">

    <!-- Hero Banner Section -->
    @if($featuredGame)
    <div class="relative rounded-3xl overflow-hidden glass-panel border border-slate-700/60 shadow-2xl p-6 md:p-10">
        <!-- Ambient background glow -->
        <div class="absolute -right-20 -top-20 w-96 h-96 bg-purple-600/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-20 -bottom-20 w-96 h-96 bg-cyan-600/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <!-- Left Info -->
            <div class="lg:col-span-7 space-y-5">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-purple-500/10 border border-purple-500/30 text-purple-400 text-xs font-semibold uppercase tracking-wider">
                    <i class="fa-solid fa-fire text-amber-400 animate-pulse"></i>
                    <span>Game Populer Minggu Ini</span>
                </div>
                <h1 class="font-outfit font-black text-3xl sm:text-5xl text-white tracking-tight leading-tight">
                    {{ $featuredGame->title }}
                </h1>
                <p class="text-slate-300 text-sm sm:text-base line-clamp-3 max-w-2xl leading-relaxed">
                    {{ $featuredGame->description ?? 'Mainkan game seru ini secara gratis langsung di browser Anda tanpa perlu install aplikasi!' }}
                </p>
                <div class="flex flex-wrap items-center gap-4 pt-2">
                    <a href="{{ route('portal.play', $featuredGame->slug) }}" 
                        class="inline-flex items-center space-x-3 px-8 py-4 rounded-2xl bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-500 hover:from-amber-400 hover:to-yellow-300 text-slate-900 font-outfit font-bold text-lg shadow-xl shadow-amber-500/40 hover:scale-105 transition-all duration-300">
                        <i class="fa-solid fa-play text-xl"></i>
                        <span>Mainkan Sekarang</span>
                    </a>
                    <div class="flex items-center space-x-4 text-xs font-medium text-slate-400">
                        <span class="inline-flex items-center px-3 py-1.5 rounded-xl bg-slate-800 border border-slate-700">
                            <i class="fa-solid fa-gamepad text-purple-400 mr-2"></i> {{ $featuredGame->category }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1.5 rounded-xl bg-slate-800 border border-slate-700">
                            <i class="fa-solid fa-circle-play text-cyan-400 mr-2"></i> {{ number_format($featuredGame->plays_count) }}x Dimainkan
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right Thumbnail Showcase -->
            <div class="lg:col-span-5 relative group">
                <a href="{{ route('portal.play', $featuredGame->slug) }}" class="block relative rounded-2xl overflow-hidden shadow-2xl border border-slate-700 group-hover:border-purple-500/60 transition-all duration-300">
                    <img src="{{ $featuredGame->thumbnail_url }}" alt="{{ $featuredGame->title }}" class="w-full h-64 sm:h-80 object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <div class="w-16 h-16 rounded-full bg-amber-500/90 text-slate-900 flex items-center justify-center shadow-2xl glow-amber scale-90 group-hover:scale-100 transition-transform">
                            <i class="fa-solid fa-play text-2xl ml-1"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Category Filter Bar & Search -->
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800/80 pb-4">
            <div>
                <h2 class="font-outfit font-bold text-2xl text-white tracking-wide">Katalog Game</h2>
                <p class="text-xs text-slate-400">Pilih kategori atau cari game HTML5 favorit Anda</p>
            </div>

            <!-- Mobile Search -->
            <form action="{{ route('portal.index') }}" method="GET" class="md:hidden">
                <div class="relative w-full">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari game..."
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl py-2 pl-10 pr-4 text-sm text-slate-200 focus:outline-none focus:border-purple-500">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                </div>
            </form>
        </div>

        <!-- Categories Pill Scrollbar -->
        <div class="flex items-center space-x-2 overflow-x-auto pb-2 scrollbar-none">
            @foreach($categories as $cat)
                @php
                    $isActive = ($category === $cat) || (!$category && $cat === 'All');
                @endphp
                <a href="{{ route('portal.index', array_merge(request()->query(), ['category' => $cat === 'All' ? null : $cat])) }}"
                   class="px-5 py-2.5 rounded-xl font-medium text-xs whitespace-nowrap transition-all duration-200 border {{ $isActive ? 'bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-900 border-amber-400 shadow-md shadow-amber-500/30' : 'bg-slate-900/80 hover:bg-slate-800 text-slate-300 border-slate-800 hover:border-slate-700' }}">
                    @if($cat === 'All') <i class="fa-solid fa-border-all mr-1.5"></i> @endif
                    {{ $cat === 'All' ? 'Semua Game' : $cat }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Game Cards Grid -->
    @if($games->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($games as $g)
                <div class="glass-card rounded-2xl overflow-hidden flex flex-col group">
                    <!-- Thumbnail Container -->
                    <div class="relative aspect-video overflow-hidden bg-slate-900">
                        <img src="{{ $g->thumbnail_url }}" alt="{{ $g->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        
                        <!-- Hover Overlay -->
                        <a href="{{ route('portal.play', $g->slug) }}" class="absolute inset-0 bg-slate-950/70 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <span class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-xl bg-amber-500 text-slate-900 font-outfit font-bold text-xs shadow-lg shadow-amber-500/50 scale-90 group-hover:scale-100 transition-transform">
                                <i class="fa-solid fa-circle-play text-sm"></i>
                                <span>MAIN SEKARANG</span>
                            </span>
                        </a>

                        <!-- Category Tag -->
                        <span class="absolute top-3 left-3 px-2.5 py-1 rounded-lg bg-slate-950/80 backdrop-blur-md text-[10px] font-bold tracking-wider text-amber-400 border border-amber-500/30 uppercase">
                            {{ $g->category }}
                        </span>
                    </div>

                    <!-- Card Body -->
                    <div class="p-5 flex-grow flex flex-col justify-between space-y-4">
                        <div>
                            <h3 class="font-outfit font-bold text-lg text-white group-hover:text-amber-400 transition-colors line-clamp-1">
                                <a href="{{ route('portal.play', $g->slug) }}">{{ $g->title }}</a>
                            </h3>
                            <p class="text-xs text-slate-400 mt-1 line-clamp-2 leading-relaxed">
                                {{ $g->description ?? 'Nikmati keseruan bermain game HTML5 secara langsung di browser tanpa perlu install.' }}
                            </p>
                        </div>

                        <!-- Card Footer Stats -->
                        <div class="flex items-center justify-between pt-3 border-t border-slate-800/80 text-[11px] text-slate-400 font-medium">
                            <span class="flex items-center space-x-1.5">
                                <i class="fa-solid fa-gamepad text-amber-400"></i>
                                <span>{{ number_format($g->plays_count) }} dimainkan</span>
                            </span>
                            <a href="{{ route('portal.play', $g->slug) }}" class="text-amber-400 hover:text-amber-300 font-semibold flex items-center space-x-1">
                                <span>Main</span>
                                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="pt-6">
            {{ $games->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="glass-panel rounded-3xl p-12 text-center max-w-xl mx-auto space-y-5 border border-slate-800">
            <div class="w-20 h-20 mx-auto rounded-full bg-slate-800/80 border border-slate-700 flex items-center justify-center text-slate-400">
                <i class="fa-solid fa-ghost text-4xl"></i>
            </div>
            <div class="space-y-2">
                <h3 class="font-outfit font-bold text-xl text-white">Belum Ada Game Ditemukan</h3>
                <p class="text-slate-400 text-sm">
                    @if($search || $category)
                        Tidak ada game yang sesuai dengan kata kunci atau kategori yang Anda pilih.
                    @else
                        Belum ada game yang diunggah ke dalam portal ini.
                    @endif
                </p>
            </div>
            @auth
                @if(Auth::user()->is_admin)
                    <a href="{{ route('admin.games.create') }}" class="inline-flex items-center space-x-2 px-6 py-3 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-medium text-sm shadow-lg shadow-purple-600/30">
                        <i class="fa-solid fa-upload"></i>
                        <span>Unggah Game Pertama</span>
                    </a>
                @endif
            @else
                <a href="{{ route('portal.index') }}" class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm">
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>Reset Filter</span>
                </a>
            @endauth
        </div>
    @endif

</div>
@endsection
