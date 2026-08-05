<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Game Musaba - SMK Muhammadiyah 1 Bantul')</title>

    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: {
                            900: '#090d16',
                            800: '#0f172a',
                            700: '#1e293b',
                            600: '#334155',
                        },
                        brand: {
                            amber: '#F5A623',
                            teal: '#0E7A72',
                            gold: '#E8960F',
                            emerald: '#10b981',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        outfit: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #090d16;
            color: #f8fafc;
            font-family: 'Inter', sans-serif;
        }

        .font-outfit {
            font-family: 'Outfit', sans-serif;
        }

        /* Glassmorphic effect */
        .glass-panel {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .glass-card {
            background: rgba(30, 41, 59, 0.6);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.06);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 30px -10px rgba(245, 166, 35, 0.25);
            border-color: rgba(245, 166, 35, 0.4);
        }

        .glow-amber {
            box-shadow: 0 0 25px -5px rgba(245, 166, 35, 0.5);
        }

        .glow-teal {
            box-shadow: 0 0 25px -5px rgba(14, 122, 114, 0.5);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #090d16;
        }
        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #8b5cf6;
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col selection:bg-purple-500 selection:text-white">

    <!-- Header Navigation Bar -->
    <header class="sticky top-0 z-50 glass-panel border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">

                <!-- Logo & Brand Name -->
                <a href="{{ route('portal.index') }}" class="flex items-center space-x-3 group">
                    <div class="w-12 h-12 rounded-full overflow-hidden flex-shrink-0 group-hover:scale-105 transition-transform duration-300 shadow-lg shadow-amber-500/30">
                        <img src="{{ asset('images/logomusaba.png') }}" alt="Logo Musaba" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <span class="font-outfit font-black text-2xl tracking-wide">
                            <span class="text-amber-400">MUSABA</span><span class="text-teal-400"> GAME</span>
                        </span>
                        <span class="block text-[10px] text-amber-400/80 font-semibold tracking-wider uppercase">SMK Muhammadiyah 1 Bantul</span>
                    </div>
                </a>

                <!-- Search Input Form -->
                <form action="{{ route('portal.index') }}" method="GET" class="hidden md:flex flex-1 max-w-md mx-8">
                    <div class="relative w-full">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari game seru..."
                            class="w-full bg-slate-900/90 border border-slate-700/70 rounded-full py-2.5 pl-11 pr-4 text-sm text-slate-200 placeholder-slate-400 focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                    </div>
                </form>

                <!-- Navigation Links & Admin Button -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('portal.index') }}" class="text-sm font-medium text-slate-300 hover:text-white transition-colors">
                        <i class="fa-solid fa-compass mr-1.5 text-purple-400"></i> Jelajah Game
                    </a>
                    
                    @auth
                        @if(Auth::user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center space-x-2 px-4 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-yellow-400 hover:from-amber-400 hover:to-yellow-300 text-slate-900 font-bold text-sm shadow-lg shadow-amber-500/30 transition-all hover:scale-105">
                                <i class="fa-solid fa-gauge-high"></i>
                                <span>Dashboard Admin</span>
                            </a>
                        @endif
                    @else
                        <a href="{{ route('admin.login') }}" class="inline-flex items-center space-x-2 px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-medium text-sm transition-all hover:border-amber-500/50">
                            <i class="fa-solid fa-lock text-amber-400"></i>
                            <span>Admin Login</span>
                        </a>
                    @endauth
                </div>

            </div>
        </div>
    </header>

    <!-- Main Content Body -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Modern Footer -->
    <footer class="mt-20 border-t border-slate-800 bg-slate-950 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-full overflow-hidden flex-shrink-0">
                        <img src="{{ asset('images/logomusaba.png') }}" alt="Logo Musaba" class="w-full h-full object-contain">
                    </div>
                    <span class="font-outfit font-bold text-amber-400">Portal Game Musaba</span>
                    <span class="text-slate-600">|</span>
                    <span class="text-xs text-slate-400">SMK Muhammadiyah 1 Bantul</span>
                </div>
                <p class="text-xs text-slate-500">&copy; {{ date('Y') }} Portal Game Musaba. SMK Muhammadiyah 1 Bantul.</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
