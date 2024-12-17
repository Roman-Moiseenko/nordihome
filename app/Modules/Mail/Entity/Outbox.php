<?php

namespace App\Modules\Mail\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $staff_id
 * @property array $emails
 * @property string $subject
 * @property string $message
 * @property array $attachments
 * @property bool $sent
 * @property Carbon $created_at //Создано
 * @property Carbon $updated_at //Отправлено
 * @property Carbon $sent_at //Отправлено
 */
class Outbox extends Model
{
    use HasFactory;

    protected $attributes = [
        'attachments' => '{}',
        'emails' => '{}',
        'message' => '',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'sent_at' => 'datetime',
        'attachments' =>'json',
        'emails' =>'json',
    ];
    protected $fillable = [
        'staff_id',
        'emails',
        'subject',
        'message',
        'attachments',
        'sent',
        'sent_at',
    ];

    public static function register(int $staff_id, array $emails, string $subject): self
    {
        return self::create([
            'staff_id' => $staff_id,
            'emails' => $emails,
            'subject' => $subject,
            'sent' => false
        ]);
    }

    public function isSent(): bool
    {
        return $this->sent == true;
    }

    public function send(): void
    {
        $this->sent_at = now();
        $this->sent = true;
        $this->save();
    }

}
