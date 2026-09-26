<?php

namespace App\Modules\Shared\Infrastructure\Storage;

use App\Modules\Shared\Application\Interfaces\PhotoStorageInterface;
use Illuminate\Contracts\Filesystem\Filesystem;
final readonly class S3PhotoStorage implements PhotoStorageInterface
{
    public function __construct(
        private Filesystem $disk,
        private ?string    $urlPrefix = null, // CDN/публичный домен, если есть
    ) {}

    public function put(string $relativePath, string $contents): void
    {
        $this->disk->put($this->key($relativePath), $contents);
    }

    public function putFromLocalFile(string $relativePath, string $localAbsolutePath): void
    {
        $stream = fopen($localAbsolutePath, 'rb');
        try {
            $this->disk->writeStream($this->key($relativePath), $stream);
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }
    }

    public function localCopy(string $relativePath): ?string
    {
        $key = $this->key($relativePath);
        if (!$this->disk->exists($key)) {
            return null;
        }

        $tmp = tempnam(sys_get_temp_dir(), 'photo_');
        $stream = $this->disk->readStream($key);
        if (!is_resource($stream)) {
            @unlink($tmp);
            return null;
        }

        $out = fopen($tmp, 'wb');
        stream_copy_to_stream($stream, $out);
        fclose($out);
        fclose($stream);

        return $tmp;
    }

    public function exists(string $relativePath): bool
    {
        return $this->disk->exists($this->key($relativePath));
    }

    public function delete(string $relativePath): void
    {
        $this->disk->delete($this->key($relativePath));
    }

    public function deleteMatching(string $directoryRelativePath, string $globPattern): void
    {
        $dir = trim($this->key($directoryRelativePath), '/');
        $regex = $this->globToRegex($globPattern);

        foreach ($this->disk->files($dir) as $path) {
            if (preg_match($regex, basename($path))) {
                $this->disk->delete($path);
            }
        }
    }

    public function url(string $relativePath): string
    {
        $key = $this->key($relativePath);

        if ($this->urlPrefix !== null && $this->urlPrefix !== '') {
            return rtrim($this->urlPrefix, '/') . '/' . $key;
        }

        return $this->disk->url($key);
    }

    public function listFiles(string $directoryRelativePath): array
    {
        $dir = trim($this->key($directoryRelativePath), '/');
        if ($dir === '') {
            return [];
        }

        return array_map(
            static fn (string $p): string => '/' . ltrim($p, '/'),
            $this->disk->allFiles($dir)
        );
    }

    private function key(string $relativePath): string
    {
        return ltrim($relativePath, '/');
    }

    private function globToRegex(string $glob): string
    {
        $regex = preg_quote($glob, '#');
        $regex = str_replace(['\*', '\?'], ['.*', '.'], $regex);
        return '#^' . $regex . '$#u';
    }
}
