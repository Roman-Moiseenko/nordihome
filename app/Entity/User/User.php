<?php

namespace App\Entity\User;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Trait\FullNameTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $status
 * @property string $email
 * @property string $phone
 * @property string $password
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, FullNameTrait;
    protected string $guard = 'user';
    public string $uploads = 'uploads/users/';
    public const STATUS_WAIT = 'wait';
    public const STATUS_ACTIVE = 'active';

    protected $fillable = [
        'email',
        'phone',
        'password',
        'status',
        'verify_token',
        'fullname_surname',
        'fullname_firstname',
        'fullname_secondname',
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

    public FullName $fullName;

    public function __construct(array $attributes = [])
    {
        $this->fullName = new FullName();
        parent::__construct($attributes);
    }

    public function isWait(): bool
    {
        return $this->status === self::STATUS_WAIT;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * @return string
     */

    public static function register(string $email, string $phone, string $password): self
    {
        return static::create([
            'email' => $email,
            'phone' => $phone,
            'password' => Hash::make($password),
            'verify_token' => Str::uuid(),
            'status' => self::STATUS_WAIT,
        ]);
    }

    public static function new(string $email, string $phone): self
    {
        return static::create([
            'email' => $email,
            'phone' => $phone,
            'password' => bcrypt(Str::random()),
            'status' => self::STATUS_ACTIVE,
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

}
