<?php

namespace App\Modules\Page\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $text_id
 * @property string $caption
 * @property string $description
 * @property int $sort
 * @property TextWidget $textWidget
 */
class TextWidgetItem extends Model
{
    public $timestamps = false;

    protected $table= "widget_text_items";

    protected $fillable = [
        'text_id',
        'sort',
        'caption',
        'description'
    ];

    public static function register(int $text_id, $caption, $description): self
    {
        return self::create([
            'text_id' => $text_id,
            'sort' => self::where('text_id', $text_id)->count(),
            'caption' => $caption,
            'description' => $description
        ]);
    }

    public function text(): BelongsTo
    {
        return $this->belongsTo(TextWidget::class, 'text_id', 'id');
    }
}
