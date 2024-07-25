<?php
declare(strict_types=1);

namespace App\Modules\Base\Entity;

use App\Jobs\ClearTempFile;
use App\Modules\Admin\Entity\Options;
use App\Modules\Shop\Parser\HttpPage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use function class_basename;
use function now;
use function public_path;

/**
 * @property int $id
 * @property int $imageable_id
 * @property string $imageable_type
 * @property string $file
 * @property string $alt
 * @property int $sort
 * @property string $type
 */
class Photo extends Model
{
    protected bool $createThumbsOnSave;
    protected bool $createThumbsOnRequest;
    protected array $watermark;

    protected array $thumbs = [];

    private string $catalogUpload;
    private string $catalogThumb;
    private string $urlThumb;

    protected $fillable = [
        'file',
        'sort',
        'alt',
        'type',
    ];

    private string $urlUpload;

    /** @var UploadedFile $fileForUpload */
    private  $fileForUpload;

    public function imageable()
    {
        return $this->morphTo();
    }

    //Генерация пути
    public function patternGeneratePath(): string
    {
        return '/' . Str::slug(class_basename($this->imageable)) . '/' . $this->imageable->id . '/';
    }

    //Создание объекта
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $options = new Options();

        $this->watermark = $options->image->watermark;

        if (empty($this->thumbs)) $this->thumbs = $options->image->thumbs;

        $this->createThumbsOnSave = $options->image->createThumbsOnSave;
        $this->createThumbsOnRequest = $options->image->createThumbsOnRequest;

        //TODO Сделать переключение  м/у getPublicPath и getStoragePath
        $this->catalogUpload = $options->image->getPublicPath('uploads');
        $this->catalogThumb =   $options->image->getPublicPath('cache');

        $this->urlUpload = $options->image->path['uploads'];
        $this->urlThumb = $options->image->path['cache'];
    }

    public static function upload(UploadedFile $file, string $type = '', int $sort = 0, string $alt = ''): self
    {
        $photo = self::make([
            'file' => $file->getClientOriginalName(),
            'sort' => $sort,
            'type' => $type,
            'alt' => $alt,
        ]);
        $photo->fileForUpload = $file;
        return $photo;
    }

    public static function uploadByUrlProxy(string $url, string $type = '', int $sort = 0, string $alt = ''): self
    {
        $storage = public_path() . '/temp/';
        $upload_file_name = basename($url);
        $http = new HttpPage();
        $content = $http->getPage($url);// dlFile($url);
        $fp = fopen($storage . $upload_file_name,'x');
        fwrite($fp, $content);
        fclose($fp);

        $upload = new UploadedFile(
            $storage . $upload_file_name,
            $upload_file_name,
            null, null, true);

        ClearTempFile::dispatch($storage . $upload_file_name)->delay(now()->addMinutes(30)); //Удаление временного файла через 30 минут
        return self::upload($upload, $type, $sort, $alt);
    }

    public static function uploadByUrl(string $url, string $type = '', int $sort = 0, string $alt = ''): self
    {
        $storage = public_path() . '/temp/';
        $upload_file_name = basename($url);
        copy($url, $storage . $upload_file_name);

        $upload = new UploadedFile(
            $storage . $upload_file_name,
            $upload_file_name,
            null, null, true);

        ClearTempFile::dispatch($storage . $upload_file_name)->delay(now()->addMinutes(30)); //Удаление временного файла через 30 минут
        return self::upload($upload, $type, $sort, $alt);
    }


    // Set и Is
    public function setSort(int $sort): void
    {
        $this->sort = $sort;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function isId(int $id): bool
    {
        return $this->id == $id;
    }

    //ВЫВОД для Фронтенда получаем URL
    final public function getUploadUrl(): string
    {
        if (empty($this->file)) return '';
        return $this->urlUpload . $this->patternGeneratePath() . $this->file;
    }

    final public function getThumbUrl(string $thumb): string
    {
        if ($this->createThumbsOnRequest) $this->createThumbs();
        return $this->urlThumb . $this->patternGeneratePath() . $this->nameFileThumb($thumb);
    }

    //Путь к файлам для переноса (для Бэкенда)
    final public function getUploadFile(): string
    {
        if (empty($this->file)) return '';
        return $this->catalogUpload . $this->patternGeneratePath() . $this->file;
    }

    final public function getThumbFile(string $thumb): string
    {
        return $this->catalogThumb . $this->patternGeneratePath() . $this->nameFileThumb($thumb);
    }

    public function newUploadFile(UploadedFile $file, string $type = null)
    {
        if ($type) $this->type = $type;
        $this->fileForUpload = $file;
        $this->uploadFile();
        $this->save();
    }

    //
    public static function boot()
    {
        parent::boot();

        self::saved(function (Photo $photo) {
            if (!empty($photo->fileForUpload)) $photo->uploadFile();
            if ($photo->createThumbsOnSave) $photo->createThumbs();
        });
        self::deleting(function (Photo $photo) {
            $photo->clearThumbs();
            $old_file = $photo->getUploadFile();
            if (is_file($old_file)) unlink($old_file);
        });
    }

    private function createThumbs(): void
    {
        if (isset($this->imageable->thumbs) && !$this->imageable->thumbs) return;//В связном объекте запрет на кешированные изображения
        foreach ($this->thumbs as $thumb => $params) {
            $thumb_file = $this->getThumbFile($thumb);
            if (is_file($this->getUploadFile()) &&
                !is_file($thumb_file) &&
                (in_array($this->ext(), ['jpg', 'png', 'jpeg']))) {
                $manager = new ImageManager(['driver' => 'gd']);
                $img = $manager->make($this->getUploadFile());
                if (isset($params['width']) && isset($params['height'])) $img->fit($params['width'], $params['height']);
                if (isset($params['watermark'])) {
                    $watermark = $manager->make($this->watermark['file']);
                    $watermark->resize((int)($img->width() * $this->watermark['size']), (int)($img->width() * $this->watermark['size']));
                    $img->insert($watermark, $this->watermark['position'], $this->watermark['offset'], $this->watermark['offset']);
                }

                $path = pathinfo($thumb_file, PATHINFO_DIRNAME);
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                $img->save($thumb_file);
            }
        }
    }

    private function uploadFile()
    {
        //Удаляем файл, если есть
        $old_file = $this->getUploadFile();
        if (is_file($old_file)) unlink($old_file);

        $this->file = $this->fileForUpload->getClientOriginalName();
        //Создаем каталог
        $path = $this->catalogUpload . $this->patternGeneratePath();
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $pathThumbs = $this->catalogThumb . $this->patternGeneratePath();
        if (!file_exists($pathThumbs)) {
            mkdir($pathThumbs, 0777, true);
        }
        //dd($path);
        //dd($this->fileForUpload->getPath());
        //TODO Копирование вместо Переносим Файл??
        //$this->fileForUpload->move($path, $this->fileForUpload->getClientOriginalName());

        copy($this->fileForUpload->getPath() . '/' . $this->fileForUpload->getFilename(),$path . $this->fileForUpload->getClientOriginalName());
        //Очищаем все thumbs
        $this->clearThumbs();
        unset($this->fileForUpload);
    }

    private function clearThumbs(): void
    {
        foreach ($this->thumbs as $thumb => $params) {
            $thumb_file = $this->getThumbFile($thumb);
            if (is_file($thumb_file)) {
                unlink($thumb_file);
            }
        }
    }

    private function nameFileThumb(string $thumb): string
    {
        return $thumb . '_' . $this->id . '.' . $this->ext();
    }

    private function ext(): string
    {
        return pathinfo($this->file, PATHINFO_EXTENSION);
    }

}
