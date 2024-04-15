<?php
declare(strict_types=1);

namespace App\Modules\Page\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $icon
 * @property string $color
 * @property string $url
 * @property int $type
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
        'type',
        'sort',
        'published'
    ];

    public static function register(string $name, string $icon, string $color, string $url, int $type): self
    {
        $sort = self::count();
        return self::create([
            'name' => $name,
            'icon' => $icon,
            'color' => $color ?? '#000000',
            'url' => $url,
            'type' => $type,
            'sort' => $sort + 1,
            'published' => false,
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
