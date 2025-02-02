<?php
declare(strict_types=1);

namespace App\Modules\Base\Entity;

use App\Jobs\ClearTempFile;
use App\Modules\Admin\Entity\Options;
use App\Modules\Base\Service\HttpPage;
use App\Modules\Setting\Entity\Setting;
use App\Modules\Setting\Repository\SettingRepository;
use App\Modules\Setting\Service\SettingService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use JetBrains\PhpStorm\Deprecated;
use function class_basename;
use function now;
use function public_path;


/**
 * @property int $id
 * @property int $imageable_id
 * @property string $imageable_type
 * @property string $file
 * @property string $alt
 * @property string $title
 * @property string $description
 * @property int $sort
 * @property string $type
 * @property bool $thumb
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

    protected $attributes = [
        'thumb' => true,
    ];
    protected $fillable = [
        'file',
        'sort',
        'alt',
        'type',
        'thumb',
        'title',
        'description',
    ];

    private string $urlUpload;

    public UploadedFile $fileForUpload;

    public function imageable()
    {
        return $this->morphTo()->withTrashed();
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

        //TODO Считывать список из БД
        if (empty($this->thumbs)) $this->thumbs = $options->image->thumbs;

        $this->createThumbsOnSave = $options->image->createThumbsOnSave;
        $this->createThumbsOnRequest = $options->image->createThumbsOnRequest;

        //TODO Сделать переключение  м/у getPublicPath и getStoragePath
        $this->catalogUpload = $options->image->getPublicPath('uploads');
        $this->catalogThumb =   $options->image->getPublicPath('cache');

        $this->urlUpload = $options->image->path['uploads'];
        $this->urlThumb = $options->image->path['cache'];
    }

    public static function upload(UploadedFile $file, string $type = '', int $sort = 0, string $alt = '', bool $thumb = true): self
    {
        $photo = self::make([
            'file' => $file->getClientOriginalName(),
            'sort' => $sort,
            'type' => $type,
            'alt' => $alt,
            'thumb' => $thumb,
        ]);
        $photo->fileForUpload = $file;
        return $photo;
    }

    #[Deprecated]
    public static function uploadByUrlProxy(string $url, string $type = '', int $sort = 0, string $alt = ''): self
    {
        return self::uploadByUrl($url, $type, $sort, $alt);
        /*
        $storage = public_path() . '/temp/';
        $upload_file_name = basename($url);
        $full_filename = $storage . uniqid() . '.' . pathinfo($upload_file_name, PATHINFO_EXTENSION);

        $http = new HttpPage();
        $content = $http->getPage($url);// dlFile($url);
        $fp = fopen($full_filename,'x');
        fwrite($fp, $content);
        fclose($fp);

        $upload = new UploadedFile(
            $full_filename,
            $upload_file_name,
            null, null, true);

        ClearTempFile::dispatch($full_filename)->delay(now()->addMinutes(10)); //Удаление временного файла через 30 минут
        return self::upload($upload, $type, $sort, $alt); */
    }

    public static function uploadByUrl(string $url, string $type = '', int $sort = 0, string $alt = '', bool $thumb = true): self
    {
        //Настройка парсера

        $is_proxy = (new SettingRepository())->getParser()->with_proxy;

        $storage = public_path() . '/temp/';
        $upload_file_name = basename($url);
        $full_filename = $storage . uniqid() . '.' . pathinfo($upload_file_name, PATHINFO_EXTENSION);

        if ($is_proxy) {
            $http = new HttpPage();
            $content = $http->getPage($url);

            $fp = fopen($full_filename,'x');
            fwrite($fp, $content);
            fclose($fp);
        } else {
            copy($url, $full_filename);
        }

        $upload = new UploadedFile(
            $full_filename,
            $upload_file_name,
            null, null, true);

        ClearTempFile::dispatch($full_filename)->delay(now()->addMinutes(10)); //Удаление временного файла через 30 минут
        return self::upload($upload, $type, $sort, $alt, $thumb);
    }

    public function newUploadFile(UploadedFile $file, string $type = null, bool $thumb = true): void
    {
        if ($type) $this->type = $type;
        $this->fileForUpload = $file;
        $this->thumb = $thumb;
        $this->uploadFile();
        $this->save();
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
        if (!$this->thumb) return '';
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

    //
    public static function boot(): void
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
        if (!$this->thumb) return;
        //if (isset($this->imageable->thumbs) && !$this->imageable->thumbs) return;//В связном объекте запрет на кешированные изображения

        foreach ($this->thumbs as $thumb => $params) {
            $thumb_file = $this->getThumbFile($thumb);
            if (is_file($this->getUploadFile()) &&
                !is_file($thumb_file) &&
                (in_array($this->ext(), ['jpg', 'png', 'jpeg']))) {
                $manager = new ImageManager(); //['driver' => 'imagick']
                $img = $manager->make($this->getUploadFile());

                if (isset($params['width']) && isset($params['height'])) {
                    if (isset($params['fit'])) { //Если установлена обрезка фото
                        $img->fit($params['width'], $params['height']);
                    } else { //Масштабирование, и заполнение пустот белым
                        $scale_w = $img->width() / $params['width'];
                        $scale_h = $img->height() / $params['height'];
                        $scale = max($scale_w, $scale_h);
                        $img->fit((int)($img->width() / $scale), (int)($img->height() / $scale),);
                        $img->resizeCanvas($params['width'], $params['height']);
                    }
                }

                if (isset($params['watermark'])) {
                    $watermark = $manager->make($this->watermark['file']);
                    $watermark->resize((int)($img->width() * $this->watermark['size']), (int)($img->width() * $this->watermark['size']));
                    $img->insert($watermark, $this->watermark['position'], $this->watermark['offset'], $this->watermark['offset']);
                }

                $path = pathinfo($thumb_file, PATHINFO_DIRNAME);
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                if ($this->ext() == 'jpg') $img->encode(null, 70);
                $img->save($thumb_file);
            }
        }
    }

    private function uploadFile(): void
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
        if ($this->thumb) {
            $pathThumbs = $this->catalogThumb . $this->patternGeneratePath();
            if (!file_exists($pathThumbs)) {
                mkdir($pathThumbs, 0777, true);
            }
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
        if (!$this->thumb) return;

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

    public function delete(): void
    {
        $this->clearThumbs();
        if (is_file($this->file)) {
            unlink($this->file);
        }
        parent::delete();
    }
}
