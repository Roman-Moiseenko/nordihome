<?php

namespace App\Modules\Feedback\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property string $social_id ID клиента в соц.сети
 * @property int $social
 * @property string $social_code код соц.сети
 * @property int $status
 * @property bool $priority
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Message[] $messages
 */
class Chat extends Model
{
    const int SOCIAL_AVITO = 8101;
    const int SOCIAL_TELEGRAM = 8102;
    const int SOCIAL_VK = 8103;
    const int SOCIAL_WHATSAPP = 8104;
    const int SOCIAL_INSTAGRAM = 8105;
    const int SOCIAL_TALK_ME = 8106;

    const int STATUS_NEW = 8200;
    const int STATUS_READ = 8201;
    const int STATUS_ANSWERED = 8202;
    const int STATUS_BLOCKED = 8203;

    protected $fillable = [
        'social_id',
        'social_code',
        'social',
    ];

    protected $casts = [
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    ];

    public static function register(int $social, string $social_id, string $social_code): self
    {
        /** @var Chat $chat */
        $chat = self::make([
            'social_id' => $social_id,
            'social_code' => $social_code,
            'social' => $social,
        ]);
        $chat->priority = false;
        $chat->status = self::STATUS_NEW;
        $chat->save();
        return $chat;
    }

    public function priority(): void
    {
        $this->priority = !$this->priority;
        $this->save();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'chat_id', 'id');
    }
}
