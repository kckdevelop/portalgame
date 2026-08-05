<?php

namespace App\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class GameExtractorService
{
    /**
     * Extract an uploaded game ZIP file to storage/app/public/games/{folderName}
     *
     * @param UploadedFile|string $zipFile
     * @param string $folderName
     * @param string|null $customEntryFile
     * @return array
     * @throws Exception
     */
    public function extractGameArchive($zipFile, string $folderName, ?string $customEntryFile = 'index.html'): array
    {
        $zipPath = $zipFile instanceof UploadedFile ? $zipFile->getRealPath() : $zipFile;

        if (!file_exists($zipPath)) {
            throw new Exception("File ZIP tidak ditemukan.");
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            throw new Exception("Gagal membuka file ZIP. Pastikan format archive valid.");
        }

        $targetDir = Storage::disk('public')->path('games/' . $folderName);

        // Clear target directory if exists
        if (File::exists($targetDir)) {
            File::deleteDirectory($targetDir);
        }

        File::makeDirectory($targetDir, 0755, true);

        // Extract ZIP content
        $zip->extractTo($targetDir);
        $zip->close();

        // Check structure: if extracted into a single wrapper folder inside ZIP, flatten it
        $this->normalizeDirectoryStructure($targetDir);

        // Locate index.html or target entry file
        $entryFile = $this->findEntryFile($targetDir, $customEntryFile);

        return [
            'success' => true,
            'folder_name' => $folderName,
            'entry_file' => $entryFile,
            'target_dir' => $targetDir,
        ];
    }

    /**
     * If the ZIP extracted a single top-level folder, move its contents to root target directory
     */
    protected function normalizeDirectoryStructure(string $targetDir): void
    {
        $items = array_diff(scandir($targetDir), ['.', '..']);
        
        // If there's only 1 item and it is a directory, flatten
        if (count($items) === 1) {
            $singleItem = $targetDir . DIRECTORY_SEPARATOR . reset($items);
            if (is_dir($singleItem)) {
                $subItems = array_diff(scandir($singleItem), ['.', '..']);
                foreach ($subItems as $item) {
                    File::move($singleItem . DIRECTORY_SEPARATOR . $item, $targetDir . DIRECTORY_SEPARATOR . $item);
                }
                File::deleteDirectory($singleItem);
            }
        }
    }

    /**
     * Find index.html or fallback html file
     */
    protected function findEntryFile(string $targetDir, ?string $preferred = 'index.html'): string
    {
        if ($preferred && File::exists($targetDir . DIRECTORY_SEPARATOR . $preferred)) {
            return $preferred;
        }

        // Search case-insensitive index.html
        $files = File::files($targetDir);
        foreach ($files as $file) {
            if (strtolower($file->getFilename()) === 'index.html') {
                return $file->getFilename();
            }
        }

        // Search any .html file at root
        foreach ($files as $file) {
            if (strtolower($file->getExtension()) === 'html' || strtolower($file->getExtension()) === 'htm') {
                return $file->getFilename();
            }
        }

        // Search recursively inside subfolders if any
        $allFiles = File::allFiles($targetDir);
        foreach ($allFiles as $file) {
            if (strtolower($file->getFilename()) === 'index.html') {
                return Str::replaceFirst($targetDir . DIRECTORY_SEPARATOR, '', $file->getRealPath());
            }
        }

        return $preferred ?: 'index.html';
    }

    /**
     * Delete game directory from storage
     */
    public function deleteGameDirectory(string $folderName): void
    {
        $targetDir = Storage::disk('public')->path('games/' . $folderName);
        if (File::exists($targetDir)) {
            File::deleteDirectory($targetDir);
        }
    }
}
