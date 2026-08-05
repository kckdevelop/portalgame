@extends('layouts.app')

@section('title', $game->title . ' - GameHub HTML5')

@push('styles')
<style>
    /* Fullscreen Mode CSS Styles */
    #gameFrameContainer:fullscreen {
        width: 100vw !important;
        height: 100vh !important;
        max-width: 100vw !important;
        max-height: 100vh !important;
        border-radius: 0 !important;
        border: none !important;
        margin: 0 !important;
        padding: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        background-color: #000 !important;
        z-index: 999999 !important;
    }
    #gameFrameContainer:-webkit-full-screen {
        width: 100vw !important;
        height: 100vh !important;
        max-width: 100vw !important;
        max-height: 100vh !important;
        border-radius: 0 !important;
        border: none !important;
        margin: 0 !important;
        padding: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        background-color: #000 !important;
        z-index: 999999 !important;
    }
    #gameFrameContainer:-moz-full-screen {
        width: 100vw !important;
        height: 100vh !important;
        max-width: 100vw !important;
        max-height: 100vh !important;
        border-radius: 0 !important;
        border: none !important;
        margin: 0 !important;
        padding: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        background-color: #000 !important;
        z-index: 999999 !important;
    }
    #gameFrameContainer.is-fullscreen {
        width: 100vw !important;
        height: 100vh !important;
        max-width: 100vw !important;
        max-height: 100vh !important;
        border-radius: 0 !important;
        border: none !important;
        margin: 0 !important;
        padding: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        background-color: #000 !important;
        z-index: 999999 !important;
    }

    #gameFrameContainer:fullscreen #gameFrameWrapper {
        flex: 1 1 0% !important;
        width: 100% !important;
        height: 100% !important;
        max-height: none !important;
        aspect-ratio: auto !important;
    }
    #gameFrameContainer:-webkit-full-screen #gameFrameWrapper {
        flex: 1 1 0% !important;
        width: 100% !important;
        height: 100% !important;
        max-height: none !important;
        aspect-ratio: auto !important;
    }
    #gameFrameContainer:-moz-full-screen #gameFrameWrapper {
        flex: 1 1 0% !important;
        width: 100% !important;
        height: 100% !important;
        max-height: none !important;
        aspect-ratio: auto !important;
    }
    #gameFrameContainer.is-fullscreen #gameFrameWrapper {
        flex: 1 1 0% !important;
        width: 100% !important;
        height: 100% !important;
        max-height: none !important;
        aspect-ratio: auto !important;
    }

    #gameFrameContainer:fullscreen #gameIframe {
        width: 100% !important;
        height: 100% !important;
        border-radius: 0 !important;
        border: none !important;
    }
    #gameFrameContainer:-webkit-full-screen #gameIframe {
        width: 100% !important;
        height: 100% !important;
        border-radius: 0 !important;
        border: none !important;
    }
    #gameFrameContainer:-moz-full-screen #gameIframe {
        width: 100% !important;
        height: 100% !important;
        border-radius: 0 !important;
        border: none !important;
    }
    #gameFrameContainer.is-fullscreen #gameIframe {
        width: 100% !important;
        height: 100% !important;
        border-radius: 0 !important;
        border: none !important;
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-8">

    <!-- Breadcrumb Navigation -->
    <nav class="flex items-center space-x-2 text-xs text-slate-400">
        <a href="{{ route('portal.index') }}" class="hover:text-purple-400 transition-colors">Home</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-600"></i>
        <a href="{{ route('portal.index', ['category' => $game->category]) }}" class="hover:text-purple-400 transition-colors">{{ $game->category }}</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-600"></i>
        <span class="text-slate-200 font-medium truncate">{{ $game->title }}</span>
    </nav>

    <!-- Game Player Container (Theater Mode) -->
    <div class="space-y-4">

        <!-- Responsive Iframe Frame Wrapper -->
        <div id="gameFrameContainer" class="relative w-full rounded-2xl overflow-hidden glass-panel border border-slate-700/80 shadow-2xl bg-slate-950">
            
            <div id="gameFrameWrapper" class="relative w-full aspect-video max-h-[80vh] flex items-center justify-center transition-all duration-300">
                <iframe id="gameIframe"
                        src="{{ $game->play_url }}"
                        title="{{ $game->title }}"
                        class="w-full h-full border-0 rounded-2xl bg-black"
                        allow="fullscreen; autoplay; payment; accelerometer; gyroscope; microphone"
                        allowfullscreen
                        sandbox="allow-scripts allow-same-origin allow-forms allow-popups allow-modals">
                </iframe>
            </div>

            <!-- Floating Control Bar -->
            <div class="flex items-center justify-between px-6 py-3.5 bg-slate-900/90 border-t border-slate-800 text-xs text-slate-300 flex-shrink-0">
                
                <div class="flex items-center space-x-3">
                    <span class="font-outfit font-bold text-white text-base">{{ $game->title }}</span>
                    <span class="px-2.5 py-0.5 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 text-[10px] uppercase font-bold">
                        {{ $game->category }}
                    </span>
                </div>

                <div class="flex items-center space-x-3">
                    <!-- Refresh Button -->
                    <button type="button" onclick="refreshGameFrame()" 
                        class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition-all text-xs">
                        <i class="fa-solid fa-rotate-right"></i>
                        <span>Muat Ulang</span>
                    </button>

                    <!-- Fullscreen Button -->
                    <button type="button" id="fullscreenBtn" onclick="toggleGameFullscreen()"
                        class="inline-flex items-center space-x-1.5 px-4 py-1.5 rounded-lg bg-purple-600 hover:bg-purple-500 text-white font-medium shadow-md shadow-purple-600/30 transition-all text-xs">
                        <i class="fa-solid fa-expand"></i>
                        <span>Layar Penuh</span>
                    </button>
                </div>

            </div>

        </div>

    </div>

    <!-- Game Information & Controls -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left: Game Details -->
        <div class="lg:col-span-8 space-y-6">
            <div class="glass-panel rounded-2xl p-6 border border-slate-800 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-800 pb-4">
                    <div>
                        <h2 class="font-outfit font-bold text-2xl text-white">{{ $game->title }}</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Diunggah pada {{ $game->created_at->format('d M Y') }}</p>
                    </div>

                    <div class="flex items-center space-x-4 text-xs">
                        <div class="px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-300">
                            <i class="fa-solid fa-circle-play text-cyan-400 mr-1.5"></i>
                            <span class="font-bold text-white">{{ number_format($game->plays_count) }}</span> Dimainkan
                        </div>
                        <div class="px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-300">
                            <i class="fa-solid fa-eye text-purple-400 mr-1.5"></i>
                            <span class="font-bold text-white">{{ number_format($game->views_count) }}</span> Dilihat
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <h4 class="font-outfit font-semibold text-sm text-slate-200">Deskripsi Game</h4>
                    <p class="text-slate-300 text-sm leading-relaxed whitespace-pre-line">
                        {{ $game->description ?? 'Nikmati keseruan bermain game HTML5 secara langsung di browser tanpa perlu install.' }}
                    </p>
                </div>

                <div class="pt-4 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
                    <span><i class="fa-solid fa-shield-halved text-emerald-400 mr-1.5"></i> Game dijamin aman &amp; bebas iklan mengganggu</span>
                    <a href="{{ route('portal.index') }}" class="text-purple-400 hover:text-purple-300 font-medium">
                        <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Katalog
                    </a>
                </div>
            </div>
        </div>

        <!-- Right: Related Games -->
        <div class="lg:col-span-4 space-y-4">
            <h3 class="font-outfit font-bold text-lg text-white">Game Lainnya</h3>
            <div class="space-y-3">
                @foreach($relatedGames as $rg)
                    <a href="{{ route('portal.play', $rg->slug) }}" class="glass-card rounded-xl p-3 flex items-center space-x-3 group">
                        <img src="{{ $rg->thumbnail_url }}" alt="{{ $rg->title }}" class="w-16 h-16 rounded-lg object-cover bg-slate-900 flex-shrink-0 group-hover:scale-105 transition-transform">
                        <div class="flex-grow min-w-0">
                            <h4 class="font-outfit font-semibold text-sm text-slate-200 group-hover:text-purple-400 transition-colors truncate">
                                {{ $rg->title }}
                            </h4>
                            <span class="text-[10px] text-purple-400 font-medium uppercase">{{ $rg->category }}</span>
                            <div class="text-[11px] text-slate-400 mt-1">
                                <i class="fa-solid fa-circle-play text-[10px] text-cyan-400 mr-1"></i> {{ number_format($rg->plays_count) }}x
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    function refreshGameFrame() {
        const iframe = document.getElementById('gameIframe');
        if (iframe) {
            iframe.src = iframe.src;
        }
    }

    function toggleGameFullscreen() {
        const container = document.getElementById('gameFrameContainer');
        if (!container) return;

        const isFS = !!(document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement);

        if (!isFS) {
            if (container.requestFullscreen) {
                container.requestFullscreen();
            } else if (container.webkitRequestFullscreen) {
                container.webkitRequestFullscreen();
            } else if (container.mozRequestFullScreen) {
                container.mozRequestFullScreen();
            } else if (container.msRequestFullscreen) {
                container.msRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
        }
    }

    function handleFullscreenChange() {
        const container = document.getElementById('gameFrameContainer');
        const fullscreenBtn = document.getElementById('fullscreenBtn');
        const isFS = !!(document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement);

        if (container) {
            if (isFS) {
                container.classList.add('is-fullscreen');
            } else {
                container.classList.remove('is-fullscreen');
            }
        }

        if (fullscreenBtn) {
            if (isFS) {
                fullscreenBtn.innerHTML = '<i class="fa-solid fa-compress"></i> <span>Keluar Layar Penuh</span>';
            } else {
                fullscreenBtn.innerHTML = '<i class="fa-solid fa-expand"></i> <span>Layar Penuh</span>';
            }
        }
    }

    document.addEventListener('fullscreenchange', handleFullscreenChange);
    document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
    document.addEventListener('mozfullscreenchange', handleFullscreenChange);
    document.addEventListener('MSFullscreenChange', handleFullscreenChange);
</script>
@endpush
