<?php
declare(strict_types=1);

namespace App\Modules\Guide\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 */
class Country extends Model
{
    protected $table = 'guide_country';
    public $timestamps = false;
    protected $fillable = [
        'name',
    ];
}
