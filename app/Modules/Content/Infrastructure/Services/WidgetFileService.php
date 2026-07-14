<?php

declare(strict_types=1);

namespace App\Modules\Content\Infrastructure\Services;

use Illuminate\Support\Facades\File;

class WidgetFileService
{
    private const string TEMPLATES_PATH = 'app/views/widgets';

    public function createTemplateFile(string $category, string $slug): void
    {
        $filePath = $this->getTemplatePath($category, $slug);

        if (!File::exists($filePath)) {
            File::ensureDirectoryExists(dirname($filePath));
            File::put($filePath, '');
        }
    }

    public function renameTemplateFile(string $category, string $oldSlug, string $newSlug): void
    {
        $oldPath = $this->getTemplatePath($category, $oldSlug);
        $newPath = $this->getTemplatePath($category, $newSlug);

        if (!File::exists($oldPath)) {
            $this->createTemplateFile($category, $newSlug);
            return;
        }

        File::move($oldPath, $newPath);
    }

    public function moveTemplateFile(string $oldCategory, string $oldSlug, string $newCategory, string $newSlug): void
    {
        $oldPath = $this->getTemplatePath($oldCategory, $oldSlug);

        if (!File::exists($oldPath)) {
            $this->createTemplateFile($newCategory, $newSlug);
            return;
        }

        $newPath = $this->getTemplatePath($newCategory, $newSlug);

        File::ensureDirectoryExists(dirname($newPath));
        File::move($oldPath, $newPath);
    }

    public function deleteTemplateFile(string $category, string $slug): void
    {
        $filePath = $this->getTemplatePath($category, $slug);

        if (File::exists($filePath)) {
            File::delete($filePath);
        }
    }

    public function templateFileExists(string $category, string $slug): bool
    {
        return File::exists($this->getTemplatePath($category, $slug));
    }

    public function getContent(string $category, string $slug): string
    {
        $filePath = $this->getTemplatePath($category, $slug);

        if (!File::exists($filePath)) {
            return '';
        }

        return File::get($filePath);
    }

    public function saveContent(string $category, string $slug, string $content): void
    {
        $filePath = $this->getTemplatePath($category, $slug);

        File::ensureDirectoryExists(dirname($filePath));
        File::put($filePath, $content);
    }

    private function getTemplatePath(string $category, string $slug): string
    {
        return storage_path(self::TEMPLATES_PATH . '/' . $category . '/' . $slug . '.blade.php');
    }
}
