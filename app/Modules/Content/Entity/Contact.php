<?php
declare(strict_types=1);

namespace App\Modules\Content\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $channel
 * @property string $svg
 * @property string $icon
 * @property string $color
 * @property string $url
 * @property int $sort
 * @property bool $published
 */
class Contact extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'name',
        'icon',
        'color',
        'url',
        'sort',
        'published',
        'slug',
        'svg',
        'channel',
    ];

    public static function register(string $name, string $icon, string $color, string $url,
                                     string $slug, ?string $svg = null, ?string $channel = null): self
    {
        $sort = self::count();
        return self::create([
            'name' => $name,
            'icon' => $icon,
            'color' => empty($color) ? '#000000' : $color,
            'url' => $url,
            'sort' => $sort + 1,
            'published' => false,
            'slug' => $slug,
            'svg' => $svg,
            'channel' => $channel,
        ]);
    }

    public function isDraft(): bool
    {
        return $this->published == false;
    }

    public function published(): void
    {
        $this->update(['published' => true]);
    }

    public function draft(): void
    {
        $this->update(['published' => false]);
    }
}
