<?php

declare(strict_types=1);

namespace App\Modules\Marketplace\Application\Services\Avito;

use App\Modules\Shared\Domain\Entities\PhotoEntity;
use App\Modules\Shared\Domain\Interfaces\PhotoRepositoryInterface;
use App\Modules\Shared\Domain\ValueObjects\PhotoType;
use App\Modules\Shared\Infrastructure\Mappers\ModelTypeMapper;
use App\Modules\Shared\Infrastructure\Models\Photo;
use App\Modules\Shared\Infrastructure\Services\PhotoService;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

/**
 * Обработка изображений объявлений Авито.
 *
 * Из загружаемого (или копируемого) файла создаётся итоговое изображение
 * 1280×960 с наложением водяного знака, сгенерированного из adId. Именно
 * этот обработанный файл становится «оригиналом» объявления — исходный
 * файл в исходном виде не сохраняется. Использует PhotoService для хранения.
 */
class AvitoProductImageService
{
    public const string MODEL_TYPE = 'marketplace.avito-product';

    public const int WIDTH = 1280;
    public const int HEIGHT = 960;

    /** Высота водяного знака: 10% от высоты (960px → 96px). */
    public const int WATERMARK_HEIGHT = 96;

    /** Прозрачность знака 99% (opacity 1%, т.к. Intervention: 100 — непрозрачный). */
    public const int WATERMARK_OPACITY = 1;

    private const string FONT_PATH = 'resources/webfonts/AdvertaCyrillicBold.otf';

    public function __construct(
        private readonly PhotoService $photoService,
        private readonly PhotoRepositoryInterface $photoRepository,
    ) {}

    /**
     * Загрузка нового изображения с обработкой.
     */
    public function upload(int $avitoProductId, string $adId, UploadedFile $file): PhotoEntity
    {
        $fileName = $this->storeFile(
            $avitoProductId,
            $adId,
            $file->getPathname(),
            $file->getClientOriginalName(),
        );

        return $this->persist($avitoProductId, $fileName);
    }

    /**
     * Копирование изображения из существующего фото (товара каталога или
     * общего изображения) с обработкой.
     */
    public function copy(int $avitoProductId, string $adId, int $sourcePhotoId): ?PhotoEntity
    {
        $source = Photo::find($sourcePhotoId);
        if ($source === null || empty($source->file)) {
            return null;
        }

        // Получаем локальную копию исходника через драйвер хранилища (local/s3).
        $sourcePath = $this->photoService->localCopy(
            $source->model_type,
            $source->imageable_id,
            $source->file,
        );

        if ($sourcePath === null || !is_file($sourcePath)) {
            return null;
        }

        try {
            $fileName = $this->storeFile($avitoProductId, $adId, $sourcePath, $source->file);
        } finally {
            // Для S3 localCopy возвращает временный файл — удаляем его.
            $this->photoService->cleanupTemp($sourcePath);
        }

        return $this->persist($avitoProductId, $fileName);
    }

    private function storeFile(int $avitoProductId, string $adId, string $sourcePath, string $originalName): string
    {
        $processed = $this->process($sourcePath, $adId);

        try {
            $name = pathinfo($originalName, PATHINFO_FILENAME) . '.jpg';
            $processedFile = new UploadedFile($processed, $name, 'image/jpeg', null, true);

            return $this->photoService->uploadFile(self::MODEL_TYPE, $avitoProductId, $processedFile);
        } finally {
            @unlink($processed);
        }
    }

    /**
     * Обрезает исходный файл до 1280×960, накладывает водяной знак и
     * возвращает путь к временному обработанному файлу (jpg).
     */
    private function process(string $sourcePath, string $adId): string
    {
        $manager = new ImageManager();

        $img = $manager->make($sourcePath);
        $img->fit(self::WIDTH, self::HEIGHT);

        $watermark = $this->buildWatermark($adId, $manager);
        if ($watermark !== null) {
            $watermark->opacity(self::WATERMARK_OPACITY);

            $maxX = max(0, $img->width() - $watermark->width());
            $maxY = max(0, $img->height() - $watermark->height());
            $x = $maxX > 0 ? random_int(0, $maxX) : 0;
            $y = $maxY > 0 ? random_int(0, $maxY) : 0;

            $img->insert($watermark, 'top-left', $x, $y);
        }

        $tmp = tempnam(sys_get_temp_dir(), 'avito_') . '.jpg';
        $img->encode('jpg', 90);
        $img->save($tmp);

        return $tmp;
    }

    /**
     * Генерирует изображение-знак из текста adId.
     */
    private function buildWatermark(string $adId, ImageManager $manager): ?Image
    {
        $adId = trim($adId);
        $fontPath = base_path(self::FONT_PATH);

        if ($adId === '' || !is_file($fontPath)) {
            return null;
        }

        try {
            $fontSize = 72;
            $bbox = imagettfbbox($fontSize, 0, $fontPath, $adId);
            if ($bbox === false) {
                return null;
            }

            $textW = abs($bbox[2] - $bbox[0]);
            $textH = abs($bbox[1] - $bbox[7]);

            // Подгоняем высоту знака к WATERMARK_HEIGHT (10% от высоты).
            if ($textH > 0) {
                $scale = self::WATERMARK_HEIGHT / $textH;
                $fontSize = (int) round($fontSize * $scale);
                $bbox = imagettfbbox($fontSize, 0, $fontPath, $adId);
                if ($bbox === false) {
                    return null;
                }
                $textW = abs($bbox[2] - $bbox[0]);
                $textH = abs($bbox[1] - $bbox[7]);
            }

            $padX = (int) round($fontSize * 0.35);
            $padY = (int) round($fontSize * 0.25);
            $width = (int) $textW + $padX * 2;
            $height = (int) $textH + $padY * 2;

            $canvas = $manager->canvas($width, $height, [0, 0, 0, 0]);
            $canvas->text($adId, $width / 2, $height / 2, function ($font) use ($fontPath, $fontSize) {
                $font->file($fontPath);
                $font->size($fontSize);
                $font->color([255, 255, 255, 1]);
                $font->align('center');
                $font->valign('middle');
            });

            return $canvas;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function persist(int $avitoProductId, string $fileName): PhotoEntity
    {
        return $this->photoRepository->save(new PhotoEntity(
            imageableId: $avitoProductId,
            imageableType: ModelTypeMapper::toFqcn(self::MODEL_TYPE),
            modelType: self::MODEL_TYPE,
            file: $fileName,
            type: new PhotoType(PhotoType::GALLERY),
        ));
    }
}
