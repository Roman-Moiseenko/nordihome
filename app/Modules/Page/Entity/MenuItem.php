<?php

namespace App\Modules\Page\Entity;



use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $menu_id
 * @property int $sort
 * @property string $name
 * @property string $url
 * @property Menu $menu
 */
class MenuItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'url',
        'sort',
    ];

    public static function new(string $name, string $url): static
    {
        return self::make([
            'name' => $name,
            'url' => $url,
            'sort' => MenuItem::get()->count()
        ]);
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'id');
    }
}
