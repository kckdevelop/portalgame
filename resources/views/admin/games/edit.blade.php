@extends('layouts.admin')

@section('title', 'Edit Game - ' . $game->title)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-outfit font-bold text-xl text-white">Edit Informasi Game</h2>
            <p class="text-xs text-slate-400">Perbarui data, thumbnail, atau file ZIP untuk game "{{ $game->title }}".</p>
        </div>
        <a href="{{ route('admin.games.index') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali
        </a>
    </div>

    <form action="{{ route('admin.games.update', $game) }}" method="POST" enctype="multipart/form-data" class="bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 space-y-6">
        @csrf
        @method('PUT')

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
            
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-300">Judul Game <span class="text-rose-400">*</span></label>
                <input type="text" name="title" value="{{ old('title', $game->title) }}" required
                    class="w-full bg-slate-950 border border-slate-700/80 rounded-xl py-2.5 px-3.5 text-sm text-slate-200 focus:outline-none focus:border-purple-500">
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-300">Kategori Game <span class="text-rose-400">*</span></label>
                <select name="category" required
                    class="w-full bg-slate-950 border border-slate-700/80 rounded-xl py-2.5 px-3.5 text-sm text-slate-200 focus:outline-none focus:border-purple-500">
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ old('category', $game->category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-slate-300">Deskripsi Game</label>
            <textarea name="description" rows="3"
                class="w-full bg-slate-950 border border-slate-700/80 rounded-xl p-3 text-sm text-slate-200 focus:outline-none focus:border-purple-500">{{ old('description', $game->description) }}</textarea>
        </div>

        <!-- Optional Replace Thumbnail & ZIP -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">

            <!-- Thumbnail -->
            <div class="space-y-2">
                <label class="block text-xs font-semibold text-slate-300">Ganti Thumbnail (Opsional)</label>
                <div class="space-y-3">
                    <img src="{{ $game->thumbnail_url }}" alt="Current Thumbnail" class="w-full h-32 object-cover rounded-xl border border-slate-800">
                    <input type="file" name="thumbnail" accept="image/*" class="text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-purple-600/20 file:text-purple-300 hover:file:bg-purple-600/30">
                </div>
            </div>

            <!-- ZIP Replacement -->
            <div class="space-y-2">
                <label class="block text-xs font-semibold text-slate-300">Ganti File ZIP Game (Opsional)</label>
                <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 space-y-3">
                    <div class="text-xs text-slate-400">
                        <span class="block font-mono text-slate-300">Folder: /storage/games/{{ $game->folder_name }}</span>
                        <span class="block text-[10px] text-slate-500">Unggah ZIP baru hanya jika ingin mengganti file game secara keseluruhan.</span>
                    </div>
                    <input type="file" name="game_zip" accept=".zip" class="text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-cyan-600/20 file:text-cyan-300 hover:file:bg-cyan-600/30">
                </div>
            </div>

        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2 border-t border-slate-800">
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-300">Entry File Utama (HTML)</label>
                <input type="text" name="entry_file" value="{{ old('entry_file', $game->entry_file) }}"
                    class="w-full bg-slate-950 border border-slate-700/80 rounded-xl py-2.5 px-3.5 text-xs text-slate-200 font-mono focus:outline-none focus:border-purple-500">
            </div>

            <div class="flex items-center space-x-3 pt-6">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $game->is_active) ? 'checked' : '' }} class="w-4 h-4 rounded bg-slate-950 border-slate-700 text-purple-600 focus:ring-purple-500">
                <label for="is_active" class="text-xs font-semibold text-slate-200 cursor-pointer">
                    Status Game Aktif (Dapat dimainkan di portal)
                </label>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-800 flex justify-end space-x-3">
            <a href="{{ route('admin.games.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium">
                Batal
            </a>
            <button type="submit" class="px-8 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-outfit font-bold text-xs shadow-lg shadow-purple-600/30 transition-all hover:scale-105">
                Simpan Perubahan
            </button>
        </div>

    </form>

</div>
@endsection
