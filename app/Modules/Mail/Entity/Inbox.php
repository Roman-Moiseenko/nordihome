<?php

namespace App\Modules\Mail\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $from
 * @property string $email
 * @property string $box //Почтовый ящик
 * @property string $subject
 * @property string $message
 * @property array $attachments
 * @property bool $read
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $read_at
 */
class Inbox extends Model
{
    use HasFactory;

    protected $attributes = [
        'message' => '',
        'attachments' => '{}',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'read_at' => 'datetime',
        'attachments' =>'json',
    ];
    protected $fillable = [
        'box',
        'from',
        'email',
        'subject',
        'message',
        'attachments',
        'read',
    ];

    public static function register(string $box, string $from, string $email, string $subject): self
    {
        return self::create([
            'box' => $box,
            'from' => $from,
            'email' => $email,
            'subject' => $subject,
            'read' => false
        ]);
    }

    public function isRead(): bool
    {
        return $this->read == true;
    }

    public function read(): void
    {
        $this->read_at = now();
        $this->read = true;
        $this->save();
    }

}
