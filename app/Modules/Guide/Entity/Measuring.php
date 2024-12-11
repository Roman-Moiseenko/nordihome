<?php
declare(strict_types=1);

namespace App\Modules\Guide\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property boolean $fractional
 */
class Measuring extends Model
{
    protected $table = 'guide_measuring';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'fractional',
    ];
}
