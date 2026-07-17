<?php

namespace App\Modules\Content\Infrastructure\Models;

use App\Modules\Catalog\Entity\Group;
use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Content\Entity\Page;
use App\Modules\Content\Entity\PostCategory;
use App\Modules\Discount\Entity\Promotion;
use App\Modules\Parser\Infrastructure\Models\ParserCategory;
use App\Modules\Parser\Infrastructure\Models\ParserProduct;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $class
 * @property string $entity
 * @property string $template_title
 * @property string $template_description
 */
class MetaTemplate extends Model
{
    const array TEMPLATES = [
        Product::class => 'Товар',
        Category::class => 'Категория',
        ParserProduct::class => 'Товар Икеа',
        ParserCategory::class => 'Категория Икеа',
        Page::class => 'Страница',
        Post::class => 'Запись',
        PostCategory::class => 'Рубрика',
        Group::class => 'Группа товаров',
        Promotion::class => 'Акция',
    ];

    protected $fillable = [
        'class',
        'entity',
    ];
    public $timestamps = false;


    public static function register(string $class, string $entity): self
    {
        return self::create([
            'class' => $class,
            'entity' => $entity,
        ]);
    }

    /**
     * Удаление из базы, для миграции
     * @param string $class
     * @return void
     */
    public static function cancel(string $class): void
    {
        $meta = self::where('class', $class)->first();
        if (!is_null($meta)) $meta->delete();
    }
}
