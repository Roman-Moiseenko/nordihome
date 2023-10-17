<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use App\UseCases\Photo\PhotoSingle;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $product_id
 * @property string $photo
 * @property string $description
 * @property int $sort
 */
class Photo extends Model implements PhotoSingle
{
    protected $fillable = [
        'photo',
        'sort',
        'description',
    ];

    protected $hidden = [
        'product_id',
    ];

    public static function register(string $file, string $description, int $product_id, int $sort = 0): self
    {
        return self::create([
           'product_id' => $product_id,
           'photo' => $file,
           'description' => $description,
           'sort' => $sort,
        ]);
    }

    public function setSort(int $sort): void
    {
        $this->sort = $sort;
    }

    public function isId(int $id): bool
    {
        return $this->id == $id;
    }


    //TODO Функция нарезки кеша и Добавления WaterMark

    public function getUploadsDirectory(): string
    {
        return 'uploads/product/' . $this->product_id . '/' . $this->photo;
    }

    public function setPhoto(string $file): void
    {
        $this->photo = $file;
    }
}
