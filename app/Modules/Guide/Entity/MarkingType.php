<?php
declare(strict_types=1);

namespace App\Modules\Guide\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property boolean $honest
 */
class MarkingType extends Model
{

    protected $table = 'guide_marking_type';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'honest',
    ];
}
