<?php

declare(strict_types=1);

namespace App\Modules\Shared\Infrastructure\Services;

use App\Modules\Setting\Entity\Settings;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;

/**
 * Сервис для работы с файлами изображений:
 * - загрузка/копирование файлов
 * - создание/удаление thumbs (копий)
 * - генерация url
 */
class PhotoService
{
    public const string URL_UPLOAD = '/uploads';
    public const string URL_THUMB = '/cache';

    private string $catalogUpload;
    private string $catalogThumb;

    private Settings $settings;
    private array $thumbs = [];
    public bool $createThumbsOnSave;
    private bool $createThumbsOnRequest;

    public function __construct()
    {
        $this->settings = app()->make(Settings::class);

        $this->thumbs = $this->settings->image->thumbs ?? [];
        $this->createThumbsOnSave = $this->settings->image->createThumbsOnSave ?? false;
        $this->createThumbsOnRequest = $this->settings->image->createThumbsOnRequest ?? false;

        $this->catalogUpload = public_path() . self::URL_UPLOAD;
        $this->catalogThumb = public_path() . self::URL_THUMB;
    }

    /**
     * Генерация пути: /{slug_basename_class}/{imageable_id}/
     * Для model_type "catalog.room" используем "room" как имя папки
     */
    public function patternGeneratePath(string $modelType, int $imageableId): string
    {
        $parts = explode('.', $modelType);
        $dirName = Str::slug(end($parts));

        return '/' . $dirName . '/' . $imageableId . '/';
    }

    /**
     * Загружает файл в /uploads/{path}/
     * Удаляет старый файл и все thumbs
     * Возвращает имя файла
     */
    public function uploadFile(string $modelType, int $imageableId, UploadedFile $file, ?string $oldFileName = null, bool $thumb = true): string
    {
        $path = $this->patternGeneratePath($modelType, $imageableId);
        $uploadDir = $this->catalogUpload . $path;

        // Удаляем старый файл, если есть
        if ($oldFileName) {
            $oldFile = $uploadDir . $oldFileName;
            if (is_file($oldFile)) {
                unlink($oldFile);
            }
            // Очищаем thumbs от старого файла
            $this->clearThumbs($modelType, $imageableId, $oldFileName);
        }

        // Создаем каталог для загрузок
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Создаем каталог для thumbs
        if ($thumb) {
            $thumbDir = $this->catalogThumb . $path;
            if (!is_dir($thumbDir)) {
                mkdir($thumbDir, 0777, true);
            }
        }

        $fileName = $file->getClientOriginalName();
        copy($file->getPath() . '/' . $file->getFilename(), $uploadDir . $fileName);

        return $fileName;
    }

    /**
     * Возвращает URL оригинального файла
     */
    public function getUploadUrl(string $modelType, int $imageableId, string $fileName): string
    {
        if (empty($fileName)) {
            return '';
        }
        return self::URL_UPLOAD . $this->patternGeneratePath($modelType, $imageableId) . $fileName;
    }

    /**
     * Возвращает URL thumb (копии).
     * Если createThumbsOnRequest включён — создаёт thumbs на лету.
     */
    public function getThumbUrl(int $photoId, string $modelType, int $imageableId, string $fileName, string $thumb, bool $isThumbEnabled): string
    {
        if (!$isThumbEnabled) {
            return '';
        }

        $path = $this->patternGeneratePath($modelType, $imageableId);

        if ($this->createThumbsOnRequest) {
            $this->createThumbs($photoId, $modelType, $imageableId, $fileName);
        }

        return self::URL_THUMB . $path . $this->nameFileThumb($photoId, $fileName, $thumb);
    }

    /**
     * Создаёт все thumbs для файла (по настройкам из Settings)
     */
    public function createThumbs(int $photoId, string $modelType, int $imageableId, string $fileName): void
    {
        $uploadPath = $this->catalogUpload . $this->patternGeneratePath($modelType, $imageableId) . $fileName;
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);

        if (!is_file($uploadPath)) {
            return;
        }

        if (!in_array(mb_strtolower($ext), ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return;
        }

        foreach ($this->thumbs as $params) {
            $thumbFile = $this->catalogThumb
                . $this->patternGeneratePath($modelType, $imageableId)
                . $this->nameFileThumb($photoId, $fileName, $params['name']);

            if (is_file($thumbFile)) {
                continue; // уже есть
            }

            $manager = new ImageManager();
            try {
                $img = $manager->make($uploadPath);
            } catch (\Throwable $e) {
                continue;
            }

            if (isset($params['width'], $params['height'])) {
                if (!empty($params['fit'])) {
                    $img->fit($params['width'], $params['height']);
                } else {
                    $scaleW = $img->width() / $params['width'];
                    $scaleH = $img->height() / $params['height'];
                    $scale = max($scaleW, $scaleH);
                    $img->fit((int)($img->width() / $scale), (int)($img->height() / $scale));
                    $img->resizeCanvas($params['width'], $params['height']);
                }
            }

            if (!empty($params['watermark'])) {
                $watermark = $manager->make(public_path() . $this->settings->image->watermark_file);
                $watermark->resize(
                    (int)($img->width() * $this->settings->image->watermark_size),
                    (int)($img->width() * $this->settings->image->watermark_size)
                );
                $img->insert(
                    $watermark,
                    $this->settings->image->watermark_position,
                    $this->settings->image->watermark_offset,
                    $this->settings->image->watermark_offset
                );
            }

            $thumbDir = pathinfo($thumbFile, PATHINFO_DIRNAME);
            if (!is_dir($thumbDir)) {
                mkdir($thumbDir, 0777, true);
            }

            if (in_array(mb_strtolower($ext), ['jpg', 'webp'], true)) {
                $img->encode(null, 70);
            }
            $img->save($thumbFile);
        }
    }

    /**
     * Удаляет все thumb-файлы для изображения
     */
    public function clearThumbs(string $modelType, int $imageableId, string $fileName): void
    {
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        if (!$ext) {
            return;
        }

        $path = $this->catalogThumb . $this->patternGeneratePath($modelType, $imageableId);

        if (!is_dir($path)) {
            return;
        }

        foreach ($this->thumbs as $params) {
            $thumbFile = $path . $params['name'] . '_*.' . $ext;
            foreach (glob($thumbFile) as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }

    /**
     * Удаляет оригинальный файл и все thumbs
     */
    public function deleteFile(string $modelType, int $imageableId, string $fileName): void
    {
        if (empty($fileName)) {
            return;
        }

        $this->clearThumbs($modelType, $imageableId, $fileName);

        $uploadPath = $this->catalogUpload . $this->patternGeneratePath($modelType, $imageableId) . $fileName;
        if (is_file($uploadPath)) {
            unlink($uploadPath);
        }
    }

    /**
     * Формирует имя файла для thumb: {thumb}_{photoId}.{ext}
     */
    private function nameFileThumb(int $photoId, string $fileName, string $thumb): string
    {
        return $thumb . '_' . $photoId . '.' . pathinfo($fileName, PATHINFO_EXTENSION);
    }
}
