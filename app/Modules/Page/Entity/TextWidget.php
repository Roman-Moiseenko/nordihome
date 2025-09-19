<?php

namespace App\Modules\Page\Entity;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property TextWidgetItem[] items
 */
class TextWidget extends Widget
{
    protected $table="banners";

    public function itemBySlug(string $slug): ?TextWidgetItem
    {
        foreach ($this->items as $item) {
            if ($item->slug = $slug) return $item;
        }
        return null;
    }

    public function items(): HasMany
    {
        return $this->hasMany(TextWidgetItem::class, 'text_id', 'id')->orderBy('sort');
    }
}
