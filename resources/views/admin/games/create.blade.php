@extends('layouts.admin')

@section('title', 'Upload Project Game HTML5 (ZIP)')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header info -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-outfit font-bold text-xl text-white">Unggah Game Baru</h2>
            <p class="text-xs text-slate-400">Unggah file ZIP yang berisi 1 folder project game HTML5 beserta file thumbnail covernya.</p>
        </div>
        <a href="{{ route('admin.games.index') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali
        </a>
    </div>

    <!-- Upload Form Card -->
    <form action="{{ route('admin.games.store') }}" method="POST" enctype="multipart/form-data" class="bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 space-y-6">
        @csrf

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs space-y-1">
                <p class="font-bold">Terjadi kesalahan pada input form:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Game Title -->
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-300">Judul Game <span class="text-rose-400">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Space Shooter 2D"
                    class="w-full bg-slate-950 border border-slate-700/80 rounded-xl py-2.5 px-3.5 text-sm text-slate-200 focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20">
            </div>

            <!-- Game Category -->
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-300">Kategori Game <span class="text-rose-400">*</span></label>
                <select name="category" required
                    class="w-full bg-slate-950 border border-slate-700/80 rounded-xl py-2.5 px-3.5 text-sm text-slate-200 focus:outline-none focus:border-purple-500">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

        </div>

        <!-- Description -->
        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-slate-300">Deskripsi Game</label>
            <textarea name="description" rows="3" placeholder="Tuliskan petunjuk cara bermain atau penjelasan singkat game..."
                class="w-full bg-slate-950 border border-slate-700/80 rounded-xl p-3 text-sm text-slate-200 focus:outline-none focus:border-purple-500">{{ old('description') }}</textarea>
        </div>

        <!-- File Upload Section: Thumbnail & ZIP -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">

            <!-- Thumbnail Image Upload -->
            <div class="space-y-2">
                <label class="block text-xs font-semibold text-slate-300">Gambar Cover / Thumbnail <span class="text-rose-400">*</span></label>
                
                <div class="relative border-2 border-dashed border-slate-700 hover:border-purple-500 rounded-2xl p-4 text-center transition-colors bg-slate-950/50">
                    <input type="file" name="thumbnail" accept="image/*" required id="thumbnailInput" onchange="previewImage(this)"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    
                    <div id="thumbnailPlaceholder" class="space-y-2 py-3">
                        <i class="fa-solid fa-image text-3xl text-purple-400"></i>
                        <p class="text-xs text-slate-300 font-medium">Klik atau drag file gambar ke sini</p>
                        <p class="text-[10px] text-slate-500">PNG, JPG, WEBP (Maksimal 5MB)</p>
                    </div>

                    <img id="imagePreview" src="#" alt="Preview" class="hidden w-full h-40 object-cover rounded-xl border border-slate-700">
                </div>
            </div>

            <!-- Game ZIP Upload -->
            <div class="space-y-2">
                <label class="block text-xs font-semibold text-slate-300">File Project Game (.ZIP) <span class="text-rose-400">*</span></label>
                
                <div class="relative border-2 border-dashed border-slate-700 hover:border-cyan-500 rounded-2xl p-4 text-center transition-colors bg-slate-950/50">
                    <input type="file" name="game_zip" accept=".zip" required id="zipInput" onchange="updateZipInfo(this)"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    
                    <div id="zipPlaceholder" class="space-y-2 py-3">
                        <i class="fa-solid fa-file-zipper text-3xl text-cyan-400"></i>
                        <p class="text-xs text-slate-300 font-medium">Pilih file project game (.ZIP)</p>
                        <p class="text-[10px] text-slate-500">File ZIP berisi index.html &amp; aset game (Maksimal 100MB)</p>
                    </div>

                    <div id="zipFileInfo" class="hidden py-3 space-y-1">
                        <i class="fa-solid fa-circle-check text-2xl text-emerald-400"></i>
                        <p id="zipFileName" class="text-xs text-emerald-300 font-mono font-bold truncate"></p>
                        <p id="zipFileSize" class="text-[10px] text-slate-400"></p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Entry File & Status Config -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2 border-t border-slate-800">
            
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-300">Entry File Utama (HTML)</label>
                <input type="text" name="entry_file" value="{{ old('entry_file', 'index.html') }}" placeholder="index.html"
                    class="w-full bg-slate-950 border border-slate-700/80 rounded-xl py-2.5 px-3.5 text-xs text-slate-200 font-mono focus:outline-none focus:border-purple-500">
                <p class="text-[10px] text-slate-500">Nama file utama saat game dijalankan (secara otomatis mendeteksi index.html).</p>
            </div>

            <div class="flex items-center space-x-3 pt-6">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked class="w-4 h-4 rounded bg-slate-950 border-slate-700 text-purple-600 focus:ring-purple-500">
                <label for="is_active" class="text-xs font-semibold text-slate-200 cursor-pointer">
                    Langsung Publikasikan (Status Aktif)
                </label>
            </div>

        </div>

        <!-- Submit Button -->
        <div class="pt-4 border-t border-slate-800 flex justify-end space-x-3">
            <a href="{{ route('admin.games.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium">
                Batal
            </a>
            <button type="submit" class="px-8 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-outfit font-bold text-xs shadow-lg shadow-purple-600/30 transition-all hover:scale-105">
                <i class="fa-solid fa-cloud-arrow-up mr-1.5"></i> Ekstrak &amp; Simpan Game
            </button>
        </div>

    </form>

</div>
@endsection

@push('scripts')
<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const placeholder = document.getElementById('thumbnailPlaceholder');
                const img = document.getElementById('imagePreview');
                placeholder.classList.add('hidden');
                img.src = e.target.result;
                img.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function updateZipInfo(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const placeholder = document.getElementById('zipPlaceholder');
            const info = document.getElementById('zipFileInfo');
            const nameEl = document.getElementById('zipFileName');
            const sizeEl = document.getElementById('zipFileSize');

            placeholder.classList.add('hidden');
            info.classList.remove('hidden');
            nameEl.textContent = file.name;
            sizeEl.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
        }
    }
</script>
@endpush
