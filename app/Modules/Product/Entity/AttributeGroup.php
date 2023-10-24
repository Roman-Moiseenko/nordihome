<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use App\Entity\Photo;
use App\Entity\Picture;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property Photo $icon
 */
class AttributeGroup extends Model
{

    public $timestamps = false;
    public $thumbs = false;

    protected $fillable = [
        'name',
    ];

    public static function register(string $name): self
    {
        return self::create([
            'name' => $name,
        ]);
    }

    public function icon()
    {
        return $this->morphOne(Photo::class, 'imageable')->withDefault();
    }

}
