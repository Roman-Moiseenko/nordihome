<?php
declare(strict_types=1);

namespace App\Modules\Page\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property bool $active
 * @property string $name
 * @property string $template
 * @property string $caption
 * @property string $description
 * @property BannerItem[] $items
 */
class Banner extends Model
{

    const PATH_TEMPLATES = 'admin.page.banner.template.';
    const BANNER_TEMPLATES = [
        'promotion-4product' => 'Акция + 4 товара',
        'row-4product' => 'Список товаров, 4 в ряд (1 ряд)',
        'row-6product' => 'Список товаров, 6 в ряд (множество рядов)',
    ];
    public $timestamps = false;

    protected $fillable = [
        'name',
        'active',
        'template',
    ];

    public static function register(string $name, string $template)
    {
        return self::create([
            'name' => $name,
            'template' => $template,
            'active' => false,
        ]);
    }

    public function addItem($file): void
    {
        $item = BannerItem::new();
        $this->items()->save(BannerItem::new());
        $item->refresh();
        $item->saveImage($file);
    }

    public function delItem(int $id): void
    {

    }

    public function items(): HasMany
    {
        return $this->hasMany(BannerItem::class, 'banner_id', 'id');
    }
}
