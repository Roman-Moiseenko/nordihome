<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use App\Entity\Picture;
use App\Trait\PictureTrait;
use App\UseCases\Uploads\UploadsDirectory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
class Category extends Model implements UploadsDirectory
{
    use NodeTrait, HasFactory;

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

    public function getImage(): string
    {
        if (empty($this->image)) {
            return '/images/default-catalog.jpg';
        } else {
            return $this->image;
        }
    }

    public function getIcon(): string
    {
        if (empty($this->icon)) {
            return '/images/default-catalog.png';
        } else {
            return $this->icon;
        }
    }

    public function getUploadsDirectory(): string
    {
        return 'uploads/category/' . $this->id . '/';
    }

    public function setPhoto(string $file): void
    {
        throw new \DomainException('Функция setPhoto не должна вызываться в Category');
    }

    public function products(): HasMany
    {

    }

    public function allProducts(int $pagination = null):? Product
    {
        $subCategories = []; //Получаем все подкатегории всех уровней вложенности
        //Ищем товары, у которых category_id IN $subCategories
        //Ищем по вторичным категориям в таблице CategoryAssignment
        //Возвращаем
        //if $pagination == null -> все товары
        // иначе через пагинацию

        return null;
    }

}
