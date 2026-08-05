<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel - Portal Game Musaba')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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

        .glass-panel {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col md:flex-row bg-slate-950">

    <!-- Admin Sidebar -->
    <aside class="w-full md:w-64 bg-slate-900 border-r border-slate-800 flex-shrink-0 flex flex-col justify-between">
        <div class="p-6 space-y-8">
            
            <!-- Logo -->
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3">
                <div class="w-11 h-11 rounded-full overflow-hidden flex-shrink-0 shadow-lg shadow-amber-500/30">
                    <img src="{{ asset('images/logomusaba.png') }}" alt="Logo Musaba" class="w-full h-full object-contain">
                </div>
                <div>
                    <span class="font-outfit font-extrabold text-xl tracking-wide">
                        <span class="text-amber-400">MUSABA</span><span class="text-teal-400"> GAME</span>
                    </span>
                    <span class="block text-[10px] text-slate-400 font-semibold uppercase">Management Panel</span>
                </div>
            </a>

            <!-- Navigation Links -->
            <nav class="space-y-1.5 text-sm">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-amber-500 text-slate-900 shadow-md shadow-amber-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                    <i class="fa-solid fa-chart-pie w-5"></i>
                    <span>Dashboard Stats</span>
                </a>

                <a href="{{ route('admin.games.index') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.games.index') ? 'bg-amber-500 text-slate-900 shadow-md shadow-amber-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                    <i class="fa-solid fa-gamepad w-5"></i>
                    <span>Kelola Game</span>
                </a>

                <a href="{{ route('admin.games.create') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.games.create') ? 'bg-amber-500 text-slate-900 shadow-md shadow-amber-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                    <i class="fa-solid fa-cloud-arrow-up w-5"></i>
                    <span>Upload Game ZIP</span>
                </a>
            </nav>

        </div>

        <!-- Sidebar Footer -->
        <div class="p-6 border-t border-slate-800 space-y-3">
            <a href="{{ route('portal.index') }}" target="_blank" 
               class="flex items-center justify-between px-4 py-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-300 border border-slate-700 text-xs font-medium transition-all">
                <span class="flex items-center space-x-2">
                    <i class="fa-solid fa-globe text-cyan-400"></i>
                    <span>Lihat Portal Web</span>
                </span>
                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
            </a>

            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center space-x-3 px-4 py-2.5 rounded-xl text-rose-400 hover:bg-rose-500/10 text-xs font-medium transition-colors">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Keluar (Logout)</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-grow flex flex-col min-w-0">
        
        <!-- Admin Topbar -->
        <header class="h-20 bg-slate-900/60 border-b border-slate-800 px-6 sm:px-10 flex items-center justify-between">
            <h1 class="font-outfit font-bold text-xl text-white">@yield('title')</h1>
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-full bg-purple-600/30 border border-purple-500/50 flex items-center justify-center text-purple-300 font-bold text-sm">
                    {{ strtoupper(substr(Auth::user()->name ?? 'Admin', 0, 1)) }}
                </div>
                <div class="hidden sm:block text-xs">
                    <span class="block text-slate-200 font-semibold">{{ Auth::user()->name ?? 'Administrator' }}</span>
                    <span class="block text-slate-400">{{ Auth::user()->email ?? 'admin@gameportal.com' }}</span>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="p-6 sm:p-10 space-y-6 flex-grow">
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <i class="fa-solid fa-circle-check text-emerald-400 text-lg"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-sm flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <i class="fa-solid fa-circle-exclamation text-rose-400 text-lg"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @yield('content')
        </div>

    </div>

    @stack('scripts')
</body>
</html>
