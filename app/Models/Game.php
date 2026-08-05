<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'category',
        'thumbnail',
        'folder_name',
        'entry_file',
        'views_count',
        'plays_count',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'views_count' => 'integer',
        'plays_count' => 'integer',
    ];

    /**
     * Get full public URL for the game entrypoint index.html
     */
    public function getPlayUrlAttribute(): string
    {
        return asset('storage/games/' . $this->folder_name . '/' . ltrim($this->entry_file, '/'));
    }

    /**
     * Get full public URL for the thumbnail
     */
    public function getThumbnailUrlAttribute(): string
    {
        if ($this->thumbnail && Storage::disk('public')->exists($this->thumbnail)) {
            return asset('storage/' . $this->thumbnail);
        }

        // Return inline SVG dynamic placeholder if thumbnail is missing
        return 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="600" height="400" viewBox="0 0 600 400"><rect width="600" height="400" fill="%231e1b4b"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="%23818cf8" font-family="sans-serif" font-size="24" font-weight="bold">' . rawurlencode($this->title) . '</text></svg>';
    }

    public function incrementPlays(): void
    {
        $this->increment('plays_count');
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }
}
