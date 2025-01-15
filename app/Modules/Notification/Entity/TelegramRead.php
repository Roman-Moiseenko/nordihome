<?php
declare(strict_types=1);

namespace App\Modules\Notification\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $message_id
 * @property int $telegram_user_id
 * @property string $message
 */
class TelegramRead extends Model
{
    protected $table = 'telegram_reads';
    protected $fillable = [
        'message_id',
        'telegram_user_id',
        'message',
    ];

    public static function register(int $message_id, int $telegram_user_id, string $message)
    {
        self::create([
            'message_id' => $message_id,
            'telegram_user_id' => $telegram_user_id,
            'message' => $message,
        ]);
    }
}
