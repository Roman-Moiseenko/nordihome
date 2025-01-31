<?php
declare(strict_types=1);

namespace App\Modules\Page\Entity;

use App\Modules\Base\Traits\IconField;
use App\Modules\Base\Traits\ImageField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use JetBrains\PhpStorm\Deprecated;

/**
 * @property int $id
 * @property string $name
 * @property bool $active
 * @property string $template
 * @property string $caption
 * @property string $description
 * @property string $url
 * @property array $params -?
 * @property int $banner_id
 * @property Banner $banner
 * @property WidgetItem[] $items
 */
class Widget extends Model
{
    use ImageField, IconField;

    public $timestamps = false;
    protected $attributes = [
        'params' => '{}',
    ];
    public $fillable = [
        'name',
        'active',
        'template',
        'caption',
        'description',
        'params',
        'url'
    ];

    protected $casts = [
        'params' => 'json'
    ];


    public static function register(string $name, string $template): self
    {
        return self::create([
            'name' => $name,
            'active' => false,
            'template' => $template,

        ]);
    }

    public function isActive(): bool
    {
        return $this->active == true;
    }

    public function banner(): BelongsTo
    {
        return $this->belongsTo(Banner::class, 'banner_id', 'id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(WidgetItem::class, 'widget_id', 'id')->orderBy('sort');
    }


}
