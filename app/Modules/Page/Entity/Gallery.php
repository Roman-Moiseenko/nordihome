<?php

namespace App\Modules\Page\Entity;

use App\Modules\Base\Entity\Photo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Photo[] $photos
 */
class Gallery extends Model
{

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    protected $fillable = [
        'name',
        'slug',
    ];

    public static function register(string $name, string $slug = ''): self
    {
        return self::create([
            'name' => $name,
            'slug' => empty($slug) ? Str::slug($name) : $slug,
        ]);
    }

    public function photos(): MorphMany
    {
        return $this->morphMany(Photo::class, 'imageable');
    }

    public static function photo(int $id, string|null $thumb = null): string
    {
        /** @var Photo $photo */
        $photo = Photo::find($id);
        if (is_null($photo)) return '';

        return is_null($thumb) ? $photo->getUploadUrl() : $photo->getThumbUrl($thumb);
    }
}
