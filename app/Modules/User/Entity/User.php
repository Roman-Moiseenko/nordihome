<?php

namespace App\Modules\User\Entity;


use App\Modules\Accounting\Entity\Organization;
use App\Modules\Base\Casts\FullNameCast;
use App\Modules\Base\Casts\GeoAddressCast;
use App\Modules\Base\Entity\FullName;
use App\Modules\Base\Entity\GeoAddress;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Entity\Payment\PaymentHelper;
use App\Modules\Product\Entity\Review;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\Pure;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $status
 * @property string $email
 * @property string $phone
 * @property string $password
 * @property string $verify_token
 * @property int $client
 * @property bool $legal //Юридическое лицо
 * @property string $document_name
 * @property Wish[] $wishes
 * @property int $delivery
 * @property int $storage
 * @property int $organization_id
 * @property UserPayment $payment
 * @property Subscription[] $subscriptions
 * @property FullName $fullname
 * @property GeoAddress $address
 * @property Review[] $reviews
 * @property Order[] $orders
 * @property Organization $organization
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
        'legal' => false,
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

    public static function register(string $email, string $password): self
    {
        $user = User::where('email', $email)->first();
        if (!is_null($user)) throw new \DomainException('Пользователь с таким email уже существует');
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

    //*** SET-.....

    public function setPhone(string $phone)
    {
        $this->phone = preg_replace("/[^0-9]/", "", $phone);
        $this->save();
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

    //*** GET-....
    public function getLastOrder():? Order
    {
        return $this->orders()->first();
    }

    #[Pure] public function getAmountOrders(): float
    {
        $result = 0.0;
        foreach ($this->orders as $order) {
            if ($order->isCompleted()) {
                $result += $order->getExpenseAmount();
            } else {
                $result += $order->getTotalAmount();
            }
        }
        return $result;
    }

    public function getDocumentName(): string
    {
        if ($this->legal) return $this->document_name;
        return 'Без договора';
    }

    public function getPublicName(): string
    {
        if (is_null($this->organization)) return $this->fullname->getFullName();
        return $this->organization->short_name . ' (' . $this->organization->inn . ')';
    }
    //RELATIONS

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'id')->orderByDesc('created_at');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'user_id', 'id');
    }

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
        if (in_array($this->delivery, [OrderExpense::DELIVERY_REGION, OrderExpense::DELIVERY_LOCAL])) {
            $address = ' (' . $this->address->address . ')';
        } else {
            $address = '';
        }
        return $type . $address;
    }

    public function StorageDefault():? int
    {
        return $this->storage;
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

    public function getReview(int $product_id): ?Review
    {
        foreach ($this->reviews as $review) {
            if ($review->isProduct($product_id)) return $review;
        }
        return null;
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }

}
