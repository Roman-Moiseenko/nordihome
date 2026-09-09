<?php
declare(strict_types=1);

namespace App\Modules\Content\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property Post[] $posts
 */
class Label extends Model
{
    public $timestamps = false;
    protected $table = 'labels';

    protected $fillable = [
        'name',
        'slug',
    ];

    public static function register(string $name): self
    {
        return static::create([
            'name' => $name,
            'slug' => Str::slug($name),
        ]);
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'labels_posts', 'label_id', 'post_id');
    }
}
