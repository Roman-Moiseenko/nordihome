<?php

namespace App\Modules\Marketplace\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kalnoy\Nestedset\NodeTrait;

/**
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property int $parent_id
 * @property int $_lft
 * @property int $_rgt
 * @property int $depth
 * @property AvitoCategory $parent
 * @property AvitoCategory[] $children
 * @property AvitoCategoryField[] $fields
 */
class AvitoCategory extends Model
{
    use NodeTrait;

    protected $fillable = [
        'name', 'slug', 'parent_id',
    ];

    public function fields(): HasMany
    {
        return $this->hasMany(AvitoCategoryField::class, 'avito_category_id');
    }
}
