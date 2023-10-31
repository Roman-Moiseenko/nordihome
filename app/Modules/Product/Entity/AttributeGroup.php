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
 * @property Attribute[] $attributes
 * @property int $sort
 */
class AttributeGroup extends Model
{

    public $timestamps = false;
    public $thumbs = false;

    protected $fillable = [
        'name', 'sort',
    ];

    public static function register(string $name): self
    {
        $max = AttributeGroup::max('sort');
        return self::create([
            'name' => $name,
            'sort' => $max + 1,
        ]);
    }

    public function isId(int $id): bool
    {
        return $this->id == $id;
    }

    public function icon()
    {
        return $this->morphOne(Photo::class, 'imageable')->withDefault();
    }
    public function attributes()
    {
        return $this->hasMany(Attribute::class, 'group_id', 'id');
    }

}
