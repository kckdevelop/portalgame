<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Portal Game Musaba</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        outfit: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-slate-950 flex items-center justify-center p-4 selection:bg-purple-500 selection:text-white">

    <div class="w-full max-w-md space-y-8">
        
        <!-- Logo Header -->
        <div class="text-center space-y-3">
            <div class="w-20 h-20 mx-auto rounded-full overflow-hidden shadow-xl shadow-amber-500/30">
                <img src="{{ asset('images/logomusaba.png') }}" alt="Logo Musaba" class="w-full h-full object-contain">
            </div>
            <h2 class="font-outfit font-black text-3xl text-white tracking-wide">
                Portal Game <span class="text-amber-400">Musaba</span>
            </h2>
            <p class="text-xs text-slate-400">SMK Muhammadiyah 1 Bantul</p>
            <p class="text-[10px] text-slate-500">Masuk untuk mengelola koleksi game HTML5</p>
        </div>

        <!-- Login Card -->
        <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-8 shadow-2xl space-y-6">
            
            @if($errors->any())
                <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs space-y-1">
                    @foreach($errors->all() as $error)
                        <p><i class="fa-solid fa-circle-exclamation mr-1.5"></i> {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('admin.login.store') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Email Input -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-300">Email Administrator</label>
                    <div class="relative">
                        <input type="email" name="email" value="{{ old('email', 'admin@gameportal.com') }}" required autofocus
                            class="w-full bg-slate-950 border border-slate-700/80 rounded-xl py-3 pl-11 pr-4 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20">
                        <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    </div>
                </div>

                <!-- Password Input -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-300">Password</label>
                    <div class="relative">
                        <input type="password" name="password" required value="password123"
                            class="w-full bg-slate-950 border border-slate-700/80 rounded-xl py-3 pl-11 pr-4 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20">
                        <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between text-xs text-slate-400">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded bg-slate-950 border-slate-700 text-purple-600 focus:ring-purple-500">
                        <span>Ingat saya</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-amber-500 to-yellow-400 hover:from-amber-400 hover:to-yellow-300 text-slate-900 font-outfit font-bold text-sm shadow-lg shadow-amber-500/30 transition-all hover:scale-[1.02]">
                    Masuk ke Dashboard
                </button>
            </form>

            <!-- Helper Box -->
            <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-[11px] text-amber-300 space-y-1">
                <p class="font-bold"><i class="fa-solid fa-key mr-1"></i> Akun Admin Default:</p>
                <p>Email: <span class="font-mono text-white">admin@gameportal.com</span></p>
                <p>Password: <span class="font-mono text-white">password123</span></p>
            </div>

        </div>

        <div class="text-center text-xs text-slate-500">
            <a href="{{ route('portal.index') }}" class="hover:text-purple-400 transition-colors">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Web Portal Game
            </a>
        </div>

    </div>

</body>
</html>
