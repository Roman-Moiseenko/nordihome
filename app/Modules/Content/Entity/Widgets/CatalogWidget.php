<?php
declare(strict_types=1);

namespace App\Modules\Content\Entity\Widgets;


use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property CatalogWidgetItem[] $items
 */
class CatalogWidget extends Widget
{

    protected $table = "widget_catalogs";


    public $fillable = [

    ];


    public function items(): HasMany
    {
        return $this->hasMany(CatalogWidgetItem::class, 'widget_id', 'id')->orderBy('sort');
    }


}
