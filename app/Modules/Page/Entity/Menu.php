<?php

namespace App\Modules\Page\Entity;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property MenuItem[] $items
 */
class Menu extends Model
{

    protected $fillable = [
        'name',
        'slug',
    ];
    public $timestamps = false;

    public static function register(string $name, string $slug): self
    {
        return self::create([
            'name' => $name,
            'slug' => empty($slug) ? Str::slug($name) : $slug,
        ]);
    }

    public function items()
    {
        return $this->hasMany(MenuItem::class, 'menu_id', 'id')->orderBy('sort');
    }
}
