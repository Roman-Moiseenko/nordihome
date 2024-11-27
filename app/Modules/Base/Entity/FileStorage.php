<?php
declare(strict_types=1);

namespace App\Modules\Base\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $fileable_id
 * @property string $fileable_type
 * @property string $file
 * @property string $title
 * @property string $type
 * @property MorphTo $fileable
 */
class FileStorage extends Model
{
    protected $table = 'file_storage';

    protected $fillable = [
        'file',
        'title',
        'type',
    ];

    private string $catalogUpload;

    public UploadedFile $fileForUpload;

    public function fileable()
    {
        return $this->morphTo()->withTrashed();
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->catalogUpload = storage_path() . '/documents/';

    }
    public static function upload(UploadedFile $file, string $type = '', string $title = ''): self
    {
        $photo = self::make([
            'file' => $file->getClientOriginalName(),
            'type' => $type,
            'title' => empty($title) ? $file->getClientOriginalName() : $title,
        ]);
        $photo->fileForUpload = $file;
        return $photo;
    }

    private function uploadFile(): void
    {
        //Удаляем файл, если есть
        $old_file = $this->getUploadFile();
        if (is_file($old_file)) unlink($old_file);

        //$this->file = $this->fileForUpload->getClientOriginalName();

        $ext = pathinfo( $this->fileForUpload->getClientOriginalName(), PATHINFO_EXTENSION);

        $this->file = Str::random(8) . '.' . $ext;
        $this->save();

        //TODO Если меняем имя
        // $this->file = new_name
        //Создаем каталог
        $path = $this->catalogUpload . $this->patternGeneratePath();
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $this->fileForUpload->move($path, $this->file);
        //$this->fileForUpload->move($path, $this->fileForUpload->getClientOriginalName());

        //copy($this->fileForUpload->getPath() . '/' . $this->fileForUpload->getFilename(),$path . $this->fileForUpload->getClientOriginalName());
        //Очищаем все thumbs
        //unset($this->fileForUpload);
    }

    final public function getUploadFile(): string
    {
        if (empty($this->file)) return '';
        return $this->catalogUpload . $this->patternGeneratePath() . $this->file;
    }

    //Генерация пути
    public function patternGeneratePath(): string
    {
        return '/' . Str::slug(class_basename($this->fileable)) . '/' . $this->fileable->id . '/';
    }

    public static function boot(): void
    {
        parent::boot();
        self::saved(function (FileStorage $file) {
            if (!empty($file->fileForUpload)) $file->uploadFile();
        });
        self::deleting(function (FileStorage $file) {
            $old_file = $file->getUploadFile();
            if (is_file($old_file)) unlink($old_file);
        });
    }
}
