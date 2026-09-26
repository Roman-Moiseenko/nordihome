<?php

declare(strict_types=1);

namespace App\Modules\Shared\Infrastructure\Services;

use App\Modules\Setting\Entity\Settings;
use App\Modules\Shared\Application\Interfaces\PhotoStorageInterface;
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
    private array $thumbs = [];

    public function __construct(
        private readonly PhotoStorageInterface $storage,
        private readonly Settings $settings,
    )
    {
        $this->thumbs = $this->settings->image->thumbs ?? [];
    }

    /**
     * Генерация пути: /{slug_basename_class}/{imageable_id}/
     * Для model_type "catalog.room" используем "room" как имя папки
     */
    private function patternGeneratePath(string $modelType, int $imageableId): string
    {
        [$module, $model] = explode('.', $modelType);
        return '/' . $module . '/' . $model . '/' . $imageableId . '/';
    }

    /**
     * Загружает файл в /uploads/{path}/
     * Удаляет старый файл и все thumbs
     * Возвращает имя файла
     */
    public function uploadFile(string $modelType, int $imageableId, UploadedFile $file, ?string $oldFileName = null): string
    {
        $path = self::URL_UPLOAD .  $this->patternGeneratePath($modelType, $imageableId);
      //  $uploadDir = $this->catalogUpload . $path;

        // Удаляем старый файл, если есть
        if ($oldFileName) $this->deleteFile($modelType, $imageableId, $oldFileName);



        //if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = $file->getClientOriginalName();
        // Кладем файл в хранилище
        $this->storage->putFromLocalFile(
            $path . $fileName,
            $file->getPath() . '/' . $file->getFilename()
        );

        //copy($file->getPath() . '/' . $file->getFilename(), $uploadDir . $fileName);

        return $fileName;
    }

    /**
     * Возвращает URL оригинального файла
     */
    public function getUploadUrl(string $modelType, int $imageableId, string $fileName): string
    {
        if (empty($fileName)) return '';
        return $this->storage->url(
            self::URL_UPLOAD . $this->patternGeneratePath($modelType, $imageableId) . $fileName
        );
       // return self::URL_UPLOAD . $this->patternGeneratePath($modelType, $imageableId) . $fileName;
    }

    /**
     * Возвращает URL thumb (копии).
     * Если createThumbsOnRequest включён — создаёт thumbs на лету.
     */
    public function getThumbUrl(int $photoId, string $modelType, int $imageableId, string $fileName, string $thumb): string
    {
        $path = $this->patternGeneratePath($modelType, $imageableId);
        $thumbName = $this->nameFileThumb($photoId, $fileName, $thumb);
       // $file = self::URL_THUMB . $path . $this->nameFileThumb($photoId, $fileName, $thumb);

        $this->createThumbs($photoId, $modelType, $imageableId, $fileName, $thumb);
        return $this->storage->url(self::URL_THUMB . $path . $thumbName);
      //  return $file;
    }

    /**
     * Создаёт все thumbs для файла (по настройкам из Settings)
     */
    private function createThumbs(int $photoId, string $modelType, int $imageableId, string $fileName, string $thumb): void
    {
        $generatePath = $this->patternGeneratePath($modelType, $imageableId);
        $uploadRelative = self::URL_UPLOAD . $generatePath . $fileName;
        //$uploadPath = $this->catalogUpload . $generatePath . $fileName;

       // if (!is_file($uploadPath)) return;
        if (!$this->storage->exists($uploadRelative)) return;//Нет загруженного файла

        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        if (!in_array(mb_strtolower($ext), ['jpg', 'jpeg', 'png', 'webp'], true)) return; //Расширение невверное

        foreach ($this->thumbs as $items) {
            if ($items['name'] == $thumb) {
                $params = $items;
                break;
            }
        }
        if (!isset($params)) return; //Нет параметров для thumb файла

        $thumbRelative = self::URL_THUMB . $generatePath . $this->nameFileThumb($photoId, $fileName, $params['name']);
        if ($this->storage->exists($thumbRelative)) return;
        $localSource = $this->storage->localCopy($uploadRelative);
        if (!$localSource) return;

/*
        $thumbFile = $this->catalogThumb
            . $generatePath
            . $this->nameFileThumb($photoId, $fileName, $params['name']);
        if (is_file($thumbFile)) return; // Файл уже есть

        $thumbDir = $this->catalogThumb . $generatePath; //Создать директорию если нет
        if (!is_dir($this->catalogThumb . $generatePath)) mkdir($thumbDir, 0777, true);

*/
        $manager = new ImageManager();
        try {
            $img = $manager->make($localSource);
        } catch (\Throwable $e) {
            $this->cleanupTemp($localSource);
            return;
        }

        // 1. ПРИВОДИМ К ПРОПОРЦИЯМ КОНЕЧНОГО ИЗОБРАЖЕНИЯ (БЕЗ РЕСАЙЗА)
        if (isset($params['width'], $params['height'])) {
            $targetAspect = $params['width'] / $params['height'];

            if (!empty($params['fit'])) {
                // ОБРЕЗКА: Вырезаем по центру кусок в нужной пропорции.
                $currentAspect = $img->width() / $img->height();
                if ($currentAspect > $targetAspect) {
                    $cropW = (int)($img->height() * $targetAspect);
                    $cropH = $img->height();
                } else {
                    $cropW = $img->width();
                    $cropH = (int)($img->width() / $targetAspect);
                }
                $img->crop($cropW, $cropH);
            } else {
                // БЕЗ ОБРЕЗКИ: Создаем БОЛЬШОЙ холст в нужной пропорции и добавляем белые поля
                $canvasH = $img->height();
                $canvasW = (int)($canvasH * $targetAspect);

                if ($canvasW < $img->width()) {
                    $canvasW = $img->width();
                    $canvasH = (int)($canvasW / $targetAspect);
                }
                $img->resizeCanvas($canvasW, $canvasH, 'center', false, '#ffffff');
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
        if (isset($params['width'], $params['height'])) {
            $img->resize($params['width'], $params['height']);
        }
        if (in_array(mb_strtolower($ext), ['jpg', 'jpeg', 'webp'], true)) {
            $img->encode(null, 70);
        }
        $tmpThumb = tempnam(sys_get_temp_dir(), 'thumb_') . '.' . $ext;
        $img->save($tmpThumb);
        try {
            $this->storage->putFromLocalFile($thumbRelative, $tmpThumb);
        } finally {
            @unlink($tmpThumb);
            $this->cleanupTemp($localSource);
        }

/*
        $thumbDir = pathinfo($thumbFile, PATHINFO_DIRNAME);
        if (!is_dir($thumbDir)) {
            mkdir($thumbDir, 0777, true);
        }
        $img->save($thumbFile);
        */
        //}
    }
    private function cleanupTemp(string $path): void
    {
        // Only delete if it's in temp dir (i.e. was a copy from S3)
        if (str_starts_with($path, sys_get_temp_dir())) {
            @unlink($path);
        }
    }
    /**
     * Удаляет все thumb-файлы для изображения
     */
    private function clearThumbs(string $modelType, int $imageableId, string $fileName): void
    {
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        if (!$ext) return;
      //  $path = $this->catalogThumb . $this->patternGeneratePath($modelType, $imageableId);
     //   if (!is_dir($path)) return;
        $thumbDir = self::URL_THUMB . $this->patternGeneratePath($modelType, $imageableId);
        foreach ($this->thumbs as $params) {
            $this->storage->deleteMatching($thumbDir, $params['name'] . '_*.' . $ext);
          //  $thumbFile = $path . $params['name'] . '_*.' . $ext;
          /*  foreach (glob($thumbFile) as $file) {
                if (is_file($file)) unlink($file);
            }*/
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

        $this->storage->delete(
            self::URL_UPLOAD . $this->patternGeneratePath($modelType, $imageableId) . $fileName
        );

        /*
        $uploadPath = $this->catalogUpload . $this->patternGeneratePath($modelType, $imageableId) . $fileName;
        if (is_file($uploadPath)) {
            unlink($uploadPath);
        }*/
    }

    /**
     * Формирует имя файла для thumb: {thumb}_{photoId}.{ext}
     */
    private function nameFileThumb(int $photoId, string $fileName, string $thumb): string
    {
        return $thumb . '_' . $photoId . '.' . pathinfo($fileName, PATHINFO_EXTENSION);
    }
}
