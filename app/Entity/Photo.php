<?php
declare(strict_types=1);

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;

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
    protected string $watermark;

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

    private  $fileForUpload;

    public function imageable()
    {
        return $this->morphTo();
    }

    //Генерация пути
    public function patternGeneratePath(): string
    {
        return '/' . strtolower(class_basename($this->imageable)) . '/' . $this->imageable->id . '/';
    }

    //Создание объекта
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        //Конфигурация
        $config = Config::get('shop-config.image');
        if (empty($this->watermark)) $this->watermark = public_path() . $config['watermark'];
        if (empty($this->thumbs)) $this->thumbs = $config['thumbs'];

        $this->createThumbsOnSave = $config['createThumbsOnSave'];
        $this->createThumbsOnRequest = $config['createThumbsOnRequest'];

        $this->catalogUpload = public_path() . '/uploads';
        $this->catalogThumb = public_path() . '/cache';
        $this->urlThumb = '/cache';
        $this->urlUpload = '/uploads';
    }

    public static function upload(UploadedFile $file, string $type = ''): self
    {
        $photo = self::new([
            'file' => $file->getClientOriginalName(),
            'sort' => 0,
            'type' => $type,
        ]);
        $photo->fileForUpload = $file;
        return $photo;
    }

/*    public static function register(string $file, int $object_id, string $alt = '', int $sort = 0): self
    {
        return self::create([
            'object_id' => $object_id,
            'file' => $file,
            'alt' => $alt,
            'sort' => $sort,
        ]);
    }
*/
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
        return $this->urlThumb . $this->patternGeneratePath() . $thumb . '.' . $this->ext();
    }

    //Путь к файлам для переноса (для Бэкенда)
    final public function getUploadFile(): string
    {
        if (empty($this->file)) return '';
        return $this->catalogUpload . $this->patternGeneratePath() . $this->file;
    }

    final public function getThumbFile(string $thumb): string
    {
        return $this->catalogThumb . $this->patternGeneratePath() . $thumb . '.' . $this->ext();
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
        foreach ($this->thumbs as $thumb => $params) {
            $thumb_file = $this->getThumbFile($thumb);
            if (is_file($this->getUploadFile()) && !is_file($thumb_file)) {
                //Создаем файл с параметрами $params
                //TODO Установить IMAGE
                //Изменяем размеры, если указаны,
                //инсертим вотермарку, если параметр true
                //Сохраняем $thumb_file
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
        //Переносим Файл
        $this->fileForUpload->move($path, $this->fileForUpload->getClientOriginalName());
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


    private function ext(): string
    {
        return pathinfo($this->file, PATHINFO_EXTENSION);
    }




}
