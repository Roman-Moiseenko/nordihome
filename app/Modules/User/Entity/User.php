<?php

namespace App\Modules\User\Entity;

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
 * @property string $email
 * @property string $phone
 * @property string $password
 * @property string $verify_token
 * @property Wish[] $wishes
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
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

    /**
     * @return string
     */

    public static function register(string $email, string $password): self
    {
        return static::create([
            'email' => $email,
            //'phone' => $phone,
            'password' => Hash::make($password),
            'verify_token' => rand(1234, 9876), //Str::uuid(),
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


    public function wishes()
    {
        return $this->hasMany(Wish::class, 'user_id', 'id');
    }
}
