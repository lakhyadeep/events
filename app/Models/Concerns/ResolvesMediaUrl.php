<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;

trait ResolvesMediaUrl
{
    /**
     * Resolve a raw stored media path or full URL to an absolute public asset URL.
     */
    protected function resolveMediaUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '//')) {
            return $path;
        }

        $cleanPath = ltrim($path, '/');

        // Self-healing fallback: If media was saved to local/private disk, copy to public disk
        if (! Storage::disk('public')->exists($cleanPath) && Storage::disk('local')->exists($cleanPath)) {
            Storage::disk('public')->put($cleanPath, Storage::disk('local')->get($cleanPath));
        }

        return asset('storage/'.$cleanPath);
    }
}
