<?php

namespace App\Modules\Feedback\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $chat_id
 * @property int $staff_id
 * @property string $text
 * @property Carbon $created_at
 *
 * @property Chat $chat
 */
class Message extends Model
{

    protected $fillable = [
        'text',
        'staff_id'
    ];

    protected $touches = ['chat'];


    public static function new(string $text, int $staff_id = null): Message
    {
        $message = self::make([
            'text' => $text,
            'staff_id' => $staff_id
        ]);
        $message->created_at = Carbon::now();
        return $message;
    }





    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }
}
