<?php

if (! function_exists('vasset')) {
    /**
     * Generate an asset path with automatic cache-busting timestamp versioning.
     */
    function vasset(string $path, ?bool $secure = null): string
    {
        $realPath = public_path($path);
        $version = file_exists($realPath) ? (string) filemtime($realPath) : '1.0';

        return asset($path, $secure).'?v='.$version;
    }
}
