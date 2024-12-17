<?php

namespace App\Modules\Mail\Entity;

use App\Modules\Mail\Mailable\AbstractMailable;
use App\Modules\Mail\Mailable\TestMail;
use App\Modules\User\Entity\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $mailable
 * @property string $user_id
 * @property string $title
 * @property string $content
 * @property array $attachments
 * @property int $count
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property User $user
 */
class SystemMail extends Model
{
    use HasFactory;

    protected $attributes = [
        'attachments' => '{}',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'attachments' =>'json',
    ];
    protected $fillable = [
        'mailable',
        'user_id',
        'content',
        'attachments',
        'count',
        'title',
    ];

    //TODO Возможно перенести в Хелпер
    const MAILABLES = [
        TestMail::class => 'Тестовое письмо',
    ];

    public static function register(AbstractMailable $mailable, int $user_id): self
    {
        return self::create([
            'mailable' => $mailable::class,
            'user_id' => $user_id,
            'title' => $mailable->envelope()->subject,
            'content' => $mailable->render(),
            'attachments' => $mailable->getFiles(),
            'count' => 1
        ]);
    }

    public function notSent()
    {
        $this->count = 0;
        $this->save();
    }

    public function getMailable(): string
    {
        return self::MAILABLES[$this->mailable];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


}
