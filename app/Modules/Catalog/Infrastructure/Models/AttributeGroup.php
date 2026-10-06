<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Models;

use App\Modules\Base\Traits\ImageField;
use App\Modules\Shared\Infrastructure\Models\Photo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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


    public function attributes(): HasMany
    {
        return $this->hasMany(Attribute::class, 'group_id', 'id');
    }

}
