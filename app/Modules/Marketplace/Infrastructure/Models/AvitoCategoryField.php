<?php

namespace App\Modules\Marketplace\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $category_id
 * @property string $tag
 * @property string $name
 * @property string $type
 * @property bool $is_required
 * @property array $allowed_values
 * @property array $depends_on
 *
 */
class AvitoCategoryField extends Model
{
    protected $fillable = [
        'category_id',
        'tag',
        'name',
        'type',
        'is_required',
        'allowed_values',
        'depends_on',
    ];
}
