<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Models;

use App\Modules\Base\Traits\ImageField;
use App\Modules\Shared\Infrastructure\Models\Photo;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $svg
 * @property Photo $icon
 * @property Attribute[] $attributes
 * @property int $sort
 */
class AttributeGroup extends Model
{

    use ImageField;

    public $timestamps = false;

    protected $fillable = [
        'name', 'sort', 'svg',
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

    public function attributes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Attribute::class, 'group_id', 'id');
    }

}
