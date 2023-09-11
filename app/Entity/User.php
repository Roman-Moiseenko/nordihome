<?php

namespace App\Entity;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $status
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $role
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const STATUS_WAIT = 'wait';
    public const STATUS_ACTIVE = 'active';

    public const ROLE_USER = 'user';
    public const ROLE_CASHIER = 'cashier';
    public const ROLE_LOGISTICS = 'logistics';
    public const ROLE_COMMODITY = 'commodity';
    public const ROLE_FINANCE = 'finance';
    public const ROLE_ADMIN = 'admin';
    public const ROLES = [
        self::ROLE_ADMIN => 'Администратор',
        self::ROLE_CASHIER => 'Кассир',
        self::ROLE_COMMODITY => 'Товаровед',
        self::ROLE_FINANCE => 'Финансист',
        self::ROLE_LOGISTICS => 'Логист',
        self::ROLE_USER => 'Клиент'
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'role',
        'verify_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'verify_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isWait(): bool
    {
        return $this->status === self::STATUS_WAIT;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }


    public static function register(string $name, string $email, string $password): self
    {
        return static::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'verify_token' => Str::uuid(),
            'status' => self::STATUS_WAIT,
            'role' => self::ROLE_USER,
        ]);
    }

    public static function new(string $name, string $email): self
    {
        return static::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt(Str::random()),
            'status' => self::STATUS_ACTIVE,
            'role' => self::ROLE_USER,
        ]);
    }

    public function verify()
    {
        if (!$this->isWait()) {
            throw new \DomainException('User is already verified.');
        }
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'verify_token' => null,
        ]);
    }

    //Роли
    public function isAdmin(): bool
    {
        return $this->role == self::ROLE_ADMIN;
    }

    public function isUser(): bool
    {
        return $this->role == self::ROLE_USER;
    }

    public function isLogistics(): bool
    {
        return $this->role == self::ROLE_LOGISTICS;
    }

    public function isCashier(): bool
    {
        return $this->role == self::ROLE_CASHIER;
    }

    public function isCommodity(): bool
    {
        return $this->role == self::ROLE_COMMODITY;
    }

    public function isFinance(): bool
    {
        return $this->role == self::ROLE_FINANCE;
    }

    public function changeRole($role): void
    {
        if (!array_key_exists($role, self::ROLES)) {
            throw new \InvalidArgumentException('Неверная роль пользователя ' . $role);
        }
        if ($this->role == $role) {
            throw new \DomainException('Роль уже назначена.');
        }

        $this->update(['role' => $role]);
    }
}
