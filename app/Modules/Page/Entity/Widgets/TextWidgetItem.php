<?php

namespace App\Modules\Page\Entity\Widgets;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $text
 * @property TextWidget $widget
 */
class TextWidgetItem extends WidgetItem
{
    protected $attributes = [
        'text' => '',
    ];

    protected $table= "widget_text_items";

    protected $fillable = [];

    public static function register(int $widget_id): self
    {
        $item = parent::new($widget_id);
        $item->save();
        return $item;
    }

    public function widget(): BelongsTo
    {
        return $this->belongsTo(TextWidget::class, 'widget_id', 'id');
    }
}
