<?php
declare(strict_types=1);

namespace App\Modules\User\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string $title - Заголовок для клиентов
 * @property string $description - Описание для клиентов
 * @property bool $active -
 * @property string $listener - Класс слушатель, который осуществляет рассылку - уникальное поле
 * @property User[] $users
 */
class Subscription extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'name',
        'title',
        'description',
        'listener',
        'active',
    ];

    public static function register(string $name, string $title, string $description, string $listener): self
    {
        return self::create([
            'name' => $name,
            'title' => $title,
            'description' => $description,
            'listener' => $listener,
            'active' => false,
        ]);
    }

    public function isActive(): bool
    {
        return $this->active == true;
    }

    public function isDraft(): bool
    {
        return $this->active == false;
    }

    public function setDraft(): void
    {
        $this->active = false;
        $this->save();
    }

    public function setActivated(): void
    {
        $this->active = true;
        $this->save();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'users_subscriptions', 'subscription_id', 'user_id');
    }
}
