<?php

namespace App\Modules\Marketplace\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property int $_lft
 * @property int $_rgt
 * @property AvitoCategory $parent
 * @property AvitoCategory[] $children
 * @property AvitoCategoryField[] $fields
 */
class AvitoCategory extends Model
{
    protected $fillable = [
        'name', 'parent_id', 'slug',
    ];


    public function fields()
    {
        return $this->hasMany(AvitoCategoryField::class);
    }
}
