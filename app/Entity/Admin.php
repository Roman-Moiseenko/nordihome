<?php
declare(strict_types=1);

namespace App\Entity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $name //Логин
 * @property string $email //Для защищенной аутентификации - простой способ
 * @property string $phone //  - сложный способ
 * @property string $password
 * @property string $role
 */
class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


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
    ];

    protected string $guard = 'admin';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public static function register(string $name, string $email, string $phone, string $password): self
    {
        return static::create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => Hash::make($password),
            'role' => self::ROLE_COMMODITY,
        ]);
    }

    public static function new(string $name, string $email, string $phone): self
    {
        return static::create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => bcrypt(Str::random()),
            'role' => self::ROLE_COMMODITY,
        ]);
    }

    //Роли

    public function isAdmin(): bool
    {
        return $this->role == self::ROLE_ADMIN;
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
