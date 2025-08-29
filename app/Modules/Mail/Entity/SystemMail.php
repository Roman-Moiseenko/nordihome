<?php

namespace App\Modules\Mail\Entity;

use App\Modules\Mail\Mailable\AbstractMailable;
use App\Modules\Mail\Mailable\ExpenseCompleted;
use App\Modules\Mail\Mailable\ExpenseDelivery;
use App\Modules\Mail\Mailable\OrderAwaitingMail;
use App\Modules\Mail\Mailable\OrderNew;
use App\Modules\Mail\Mailable\TestMail;
use App\Modules\User\Entity\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;

/**
 * @property int $id
 * @property string $mailable
 * @property int $user_id
 * @property string $title
 * @property string $content
 * @property array $attachments
 * @property int $count
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property int $systemable_id
 * @property string $systemable_type
 * @property array $emails
 * @property User $user
 */
class SystemMail extends Model
{
    use HasFactory;

    protected $attributes = [
        'attachments' => '{}',
        'emails' => '{}',
        'systemable_id' => null,
        'systemable_type' => null,
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'attachments' =>'json',
        'emails' => 'json',
    ];
    protected $fillable = [
        'mailable',
        'user_id',
        'content',
        'attachments',
        'count',
        'title',
        'emails',
    ];

    //TODO Возможно перенести в Хелпер
    const MAILABLES = [
        TestMail::class => 'Тестовое письмо',
        OrderAwaitingMail::class => 'Счет на оплату',
        OrderNew::class => 'Новый заказ',
        ExpenseCompleted::class => 'Заказ выдан',
        ExpenseDelivery::class => 'Заказ на отгрузке',
    ];

    public function systemable()
    {
        return $this->morphTo()->withTrashed();
    }

    public static function register(AbstractMailable $mailable, int $user_id, array $emails): self
    {
        return self::create([
            'mailable' => $mailable::class,
            'user_id' => $user_id,
            'title' => $mailable->envelope()->subject,
            'content' => $mailable->render(),
            'attachments' => $mailable->getFiles(),
            'count' => 1,
            'emails' => $emails,
        ]);
    }

    public function notSent(): void
    {
        $this->count = 0;
        $this->save();
    }

    public function getMailable(): string
    {
        return self::MAILABLES[$this->mailable];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


}
