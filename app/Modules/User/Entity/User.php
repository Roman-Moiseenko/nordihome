<?php

namespace App\Modules\User\Entity;


use App\Casts\FullNameCast;
use App\Casts\GeoAddressCast;
use App\Entity\FullName;
use App\Entity\GeoAddress;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Entity\Payment\PaymentHelper;
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
 * @property int $client
 * @property Wish[] $wishes
 * @property int $delivery
 * @property int $storage
// * @property UserDelivery $delivery
 * @property UserPayment $payment
 * @property Subscription[] $subscriptions
 * @property FullName $fullname
 * @property GeoAddress $address
 */

//TODO Задачи по клиентам - настройка в админке, $client  - какие цены
// и др.

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected string $guard = 'user';
    public string $uploads = 'uploads/users/';
    public const STATUS_WAIT = 'wait';
    public const STATUS_ACTIVE = 'active';

    const CLIENT_RETAIL = 7700;
    const CLIENT_BULK = 7701;
    const CLIENT_SPECIAL = 7702;

    protected $attributes = [
        'fullname' => '{}',
        'address' => '{}',
        'delivery' => OrderExpense::DELIVERY_STORAGE,
    ];

    protected $fillable = [
        'email',
        'phone',
        'password',
        'status',
        'verify_token',
        'delivery',
        'storage',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'verify_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'fullname' => FullNameCast::class,
        'address' => GeoAddressCast::class
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

    public function isBulk(): bool
    {
        return $this->client == self::CLIENT_BULK;
    }

    public function isSpecial(): bool
    {
        return $this->client == self::CLIENT_SPECIAL;
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

    /**
     * @param string $surname
     * @param string $firstname
     * @param string $secondname
     * @return void
     */
    public function setNameField(string $surname = '', string $firstname = '', string $secondname = '')
    {
        if (!empty($surname)) $this->fullname->surname = $surname;
        if (!empty($firstname)) $this->fullname->firstname = $firstname;
        if (!empty($secondname)) $this->fullname->secondname = $secondname;
        $this->save();
    }

    //RELATIONS
    public function wishes()
    {
        return $this->hasMany(Wish::class, 'user_id', 'id');
    }

    //TODO Перенести в таблицу Users или сделать UserDefault
    // delivery и payment
  /*
    public function delivery()
    {
        return $this->hasOne(UserDelivery::class, 'user_id', 'id')->withDefault();
    }
*/
    public function payment()
    {
        $payments = PaymentHelper::payments();
        $default = array_key_first($payments);

        return $this->hasOne(UserPayment::class, 'user_id', 'id')->withDefault(['class_payment' => $default]);
    }

    public function subscriptions()
    {
        return $this->belongsToMany(Subscription::class, 'users_subscriptions', 'user_id', 'subscription_id');
    }


    //*** Хелперы

    public function htmlPayment(): string
    {
        $payments = PaymentHelper::payments();
        return $payments[$this->payment->class_payment]['name'];
    }

    public function htmlDelivery(): string
    {
        $type = OrderExpense::TYPES[$this->delivery];
        //$address = $this->delivery->getAddressDelivery();
        return $type . ' ('. $this->address->address . ')';
    }

    public function StorageDefault():? int
    {
        return $this->storage;
        //if (is_null($this->storage))
        //throw new \DomainException('Назначить поле хранилище');
    }

    public function isStorage(): bool
    {
        return $this->delivery == OrderExpense::DELIVERY_STORAGE;
    }

    public function isLocal(): bool
    {
        return $this->delivery == OrderExpense::DELIVERY_LOCAL;
    }
    public function isRegion(): bool
    {
        return $this->delivery == OrderExpense::DELIVERY_REGION;
    }
}
