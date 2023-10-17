<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use App\Entity\Picture;
use App\Trait\PictureTrait;
use App\UseCases\Photo\PhotoSingle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $title
 * @property string $description
 * @property string $image
 * @property string $icon
 */
class Category extends Model implements PhotoSingle
{
    use NodeTrait;

    public $timestamps = false;

    public static function register($name, $parent_id = null, $slug = '', $title = '', $description = ''): self
    {
        return self::create([
            'name' => $name,
            'parent_id' => $parent_id,
            'slug' => empty($slug) ? Str::slug($name) : $slug,
            'title' => $title,
            'description' => $description,
        ]);
    }

    public function setImage(string $file): void
    {
        $this->image = $file;
    }

    public function setIcon(string $file): void
    {
        $this->icon = $file;
    }

    public function getUploadsDirectory(): string
    {
        return 'uploads/category/' . $this->id . '/';
    }

    public function setPhoto(string $file): void
    {
        throw new \DomainException('Функция setPhoto не должна вызываться в Category');
    }
}
