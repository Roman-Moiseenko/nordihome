<?php
declare(strict_types=1);

namespace App\Entity;

use App\Entity\User\FullName;
use App\Trait\FullNameTrait;
use App\UseCases\Uploads\UploadsDirectory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

//TODO Перенести в Модули
/**
 * @property int $id
 * @property string $name //Логин
 * @property string $email //Для защищенной аутентификации - простой способ
 * @property string $phone //  - сложный способ
 * @property string $password
 * @property string $role
 * @property bool $active //Не заблокирован
 * @property string $photo
 * @property string $post //Должность
 *
 */
class Admin extends Authenticatable implements UploadsDirectory
{
    use HasApiTokens, HasFactory, Notifiable, FullNameTrait;

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
    public const ROLE_COLORS = [
        self::ROLE_ADMIN => 'bg-success',
        self::ROLE_CASHIER => 'bg-warning',
        self::ROLE_COMMODITY => 'bg-indigo-900',
        self::ROLE_FINANCE => 'bg-pending',
        self::ROLE_LOGISTICS => 'bg-primary',
    ];

   // public FullName $fullName;

    protected string $guard = 'admin';
    //public string $uploads = 'uploads/admins/';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'active',
        'post',
        'fullname_surname',
        'fullname_firstname',
        'fullname_secondname',
    ];
    //TODO протестировать сохранения если удалить fullname_***

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function __construct(array $attributes = [])
    {
        $this->fullName = new FullName();
        parent::__construct($attributes);
    }

    public static function register(string $name, string $email, string $phone, string $password): self
    {
        return static::create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => Hash::make($password),
            'role' => self::ROLE_COMMODITY,
            'active' => true,
        ]);
    }

    public static function new(string $name, string $email, string $phone, string $password): self
    {
        return static::make([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => Hash::make($password),
            'role' => self::ROLE_COMMODITY,
            'active' => true,
        ]);
    }

    public function blocked(): void
    {
        $this->active = false;
    }

    public function isBlocked(): bool
    {
        return !$this->active;
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

    public function setRole($role): void
    {
        if (!array_key_exists($role, self::ROLES)) {
            throw new \InvalidArgumentException('Неверная роль пользователя ' . $role);
        }
        $this->role = $role;
        $this->save();
    }

    public function activated()
    {
        $this->active = true;
    }


    public function isCurrent(): bool
    {
        return Auth::guard($this->guard)->user()->id == $this->id;
    }

    public function getPhoto(): string
    {
        if (empty($this->photo)) {
            return '/images/default-user.png';
        } else {
            return $this->photo;
        }
    }

    public function getUploadsDirectory(): string
    {
        return 'uploads/admins/' . $this->id . '/';
    }

    public function setPhoto(string $file): void
    {
        $this->photo = $file;
    }
}
