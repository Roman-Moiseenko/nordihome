<?php

namespace App\Modules\Page\Entity;

use App\Modules\Discount\Entity\Promotion;
use App\Modules\Parser\Entity\CategoryParser;
use App\Modules\Parser\Entity\ProductParser;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Group;
use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $class
 * @property string $template_title
 * @property string $template_description
 */
class MetaTemplate extends Model
{
    const array TEMPLATES = [
        Product::class => 'Товар',
        Category::class => 'Категория',
        ProductParser::class => 'Товар Икеа',
        CategoryParser::class => 'Категория Икеа',
        Page::class => 'Страница',
        Post::class => 'Запись',
        PostCategory::class => 'Рубрика',
        Group::class => 'Группа товаров',
        Promotion::class => 'Акция',
    ];

    protected $fillable = [
        'class',
    ];
    public $timestamps = false;


    public static function register(string $class): self
    {
        return self::create([
            'class' => $class,
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
