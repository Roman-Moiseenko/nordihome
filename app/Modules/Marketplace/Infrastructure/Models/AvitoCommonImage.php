<?php

namespace App\Modules\Marketplace\Infrastructure\Models;

use App\Modules\Base\Traits\ImageField;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string|null $name
 * @property bool $active
 * @property int $sort
 */
class AvitoCommonImage extends Model
{
    use ImageField;

    protected $table = 'avito_common_images';

    protected $fillable = [
        'name',
        'active',
        'sort',
    ];

    protected $casts = [
        'active' => 'boolean',
        'sort' => 'integer',
    ];
}
