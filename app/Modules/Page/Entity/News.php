<?php

namespace App\Modules\Page\Entity;

use App\Modules\Base\Traits\ImageField;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property string $title
 * @property string $text
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $published_at
 * @property bool $published
 */
class News extends Model
{
    use ImageField;

    protected $fillable = [
        'title',
        'text',
    ];

    public static function register(string $title, string $text): self
    {
        return self::create([
            'title' => $title,
            'text' => $text,
        ]);

    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function isPublished(): bool
    {
        return $this->published;
    }
}
