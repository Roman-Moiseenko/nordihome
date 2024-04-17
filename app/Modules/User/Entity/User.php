<?php

namespace App\Modules\User\Entity;


use App\Modules\Delivery\Entity\UserDelivery;
use App\Modules\Order\Entity\Payment\PaymentHelper;
use App\Modules\Order\Entity\UserPayment;
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
 * @property UserDelivery $delivery
 * @property UserPayment $payment
 * @property Subscription[] $subscriptions
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

    //*** IS-...
    public function isWait(): bool
    {
        return $this->status === self::STATUS_WAIT;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isWish(int $product_id): bool
    {
        foreach ($this->wishes as $wish) {
            if ($wish->product_id == $product_id) return true;
        }
        return false;
    }

    public function isSubscription(Subscription $subscription): bool
    {
        foreach ($this->subscriptions as $item) {
            if ($item->id == $subscription->id) return true;
        }
        return false;
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

    public function setPassword(string $password)
    {
        $this->password = Hash::make($password);
        $this->save();
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

    //RELATIONS
    public function wishes()
    {
        return $this->hasMany(Wish::class, 'user_id', 'id');
    }

    //TODO Перенести в таблицу Users или сделать UserDefault
    public function delivery()
    {
        return $this->hasOne(UserDelivery::class, 'user_id', 'id');
    }

    //TODO Перенести в таблицу Users или сделать UserDefault
    public function payment()
    {
        return $this->hasOne(UserPayment::class, 'user_id', 'id');
    }

    //TODO Добавить все платежи под учет OrderPayment
    // ***


    public function subscriptions()
    {
        return $this->belongsToMany(Subscription::class, 'users_subscriptions', 'user_id', 'subscription_id');
    }

    /**
     * Платеж по умолчанию
     * @return string
     */
    public function getDefaultPayment(): string
    {
        if (is_null($this->payment)) {
            $payments = PaymentHelper::payments();
            return array_key_first($payments);
        }
        return $this->payment->class_payment;
    }
}
