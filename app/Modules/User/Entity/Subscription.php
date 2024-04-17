<?php
declare(strict_types=1);

namespace App\Modules\User\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $title - Заголовок для клиентов
 * @property string $description - Описание для клиентов
 * @property bool $published -
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
        'published',
    ];

    public static function register(string $name, string $title, string $description, string $listener): self
    {
        return self::create([
            'name' => $name,
            'title' => $title,
            'description' => $description,
            'listener' => $listener,
            'published' => false,
        ]);
    }

    public function isPublished(): bool
    {
        return $this->published == true;
    }

    public function isDraft(): bool
    {
        return $this->published == false;
    }

    public function setDraft(): void
    {
        $this->published = false;
        $this->save();
    }

    public function setPublished(): void
    {
        $this->published = true;
        $this->save();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_subscriptions', 'subscription_id', 'user_id');
    }
}
