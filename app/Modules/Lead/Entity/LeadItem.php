<?php

namespace App\Modules\Lead\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $lead_id
 * @property int $staff_id
 * @property int $type
 * @property string $comment
 * @property Carbon $created_at
 * @property Carbon $finished_at
 *
 */
class LeadItem extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'comment',
        'finished_at',
        'created_at',
    ];

    public static function new(string $comment, $finished_at): self
    {
        return self::make([
            'comment' => $comment,
            'finished_at' => $finished_at,
            'created_at' => now(),
        ]);
    }
}
