<?php
declare(strict_types=1);

namespace App\Modules\Guide\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property int $value
 */
class VAT extends Model
{
    protected $table = 'guide_v_a_t';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'value',
    ];
}
