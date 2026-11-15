<?php

namespace App\Modules\Marketplace\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name_en
 * @property string|null $name_ru
 * @property string|null $kind
 * @property string|null $field
 * @property string|null $type
 * @property bool $is_required
 */
class AvitoParameterRegistry extends Model
{
    protected $table = 'avito_parameter_registry';

    protected $fillable = [
        'name_en',
        'name_ru',
        'kind',
        'field',
        'type',
        'is_required',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];
}
