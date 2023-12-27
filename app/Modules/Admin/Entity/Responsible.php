<?php
declare(strict_types=1);

namespace App\Modules\Admin\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int taskable_id
 * @property string taskable_type
 * @property int $staff_id
 * @property Carbon $created_at
 * @property Carbon $close_at
 * @property int $status
 */
//TODO Добавить поля для учета
class Responsible extends Model
{
    const NEW_TASK = 1;
    const COMPLETED = 2;
    const TRANSFERRES = 3;

    public $timestamps = false;
    protected $fillable = [
        'staff_id',
        'status'
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'close_at' => 'datetime',
    ];


    public function taskable()
    {
        return $this->morphTo();
    }
}
