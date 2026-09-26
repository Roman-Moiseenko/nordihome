<?php

namespace App\Modules\Shared\Infrastructure\Storage;

use App\Modules\Shared\Application\Interfaces\PhotoStorageInterface;

readonly class LocalPhotoStorage implements PhotoStorageInterface
{


    public function __construct(private string $basePath
    ) {}

    public function put(string $relativePath, string $contents): void
    {
        $absolute = $this->absolute($relativePath);
        $this->ensureDir(dirname($absolute));
        file_put_contents($absolute, $contents);
    }

    public function putFromLocalFile(string $relativePath, string $localAbsolutePath): void
    {
        $absolute = $this->absolute($relativePath);
        $this->ensureDir(dirname($absolute));
        copy($localAbsolutePath, $absolute);
    }

    public function localCopy(string $relativePath): ?string
    {
        $absolute = $this->absolute($relativePath);
        return is_file($absolute) ? $absolute : null;
    }

    public function exists(string $relativePath): bool
    {
        return is_file($this->absolute($relativePath));
    }

    public function delete(string $relativePath): void
    {
        $absolute = $this->absolute($relativePath);
        if (is_file($absolute)) {
            @unlink($absolute);
        }
    }

    public function deleteMatching(string $directoryRelativePath, string $globPattern): void
    {
        $dir = $this->absolute($directoryRelativePath);
        if (!is_dir($dir)) {
            return;
        }
        foreach (glob($dir . '/' . $globPattern) ?: [] as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }

    public function url(string $relativePath): string
    {
        // Относительные пути у нас уже начинаются с "/uploads" или "/cache"
        return '/' . ltrim($relativePath, '/');
    }

    public function listFiles(string $directoryRelativePath): array
    {
        $dir = $this->absolute($directoryRelativePath);
        if (!is_dir($dir)) {
            return [];
        }

        $result = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
        );
        foreach ($iterator as $file) {
            /** @var \SplFileInfo $file */
            if ($file->isFile()) {
                $sub = substr($file->getPathname(), strlen($dir));
                $result[] = rtrim($directoryRelativePath, '/') . '/' . ltrim($sub, '/');
            }
        }
        return $result;
    }

    private function absolute(string $relativePath): string
    {
        return rtrim($this->basePath, '/') . '/' . ltrim($relativePath, '/');
    }

    private function ensureDir(string $dir): void
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }
}
