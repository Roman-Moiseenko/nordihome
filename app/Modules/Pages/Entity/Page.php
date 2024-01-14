<?php
declare(strict_types=1);

namespace App\Modules\Pages\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $title
 * @property string $description
 * @property string $template
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Page extends Model
{

    const PAGES_TEMPLATES = [
        'contact',
        'review',
        'tariff',
    ];
    protected $fillable = [
        'name',
        'slug',
        'title',
        'description',
        'template',

    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function register(string $name, string $slug, string $title, string $description, string $template): self
    {
        return self::create([
            'name' => $name,
            'slug' => empty($slug) ? Str::slug($name) : $slug,
            'title' => $title,
            'description' => $description,
            'template' => $template,
        ]);
    }
}
