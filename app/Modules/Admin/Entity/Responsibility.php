<?php
declare(strict_types=1);

namespace App\Modules\Admin\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $admin_id
 * @property int $code
 */
class Responsibility extends Model
{
    protected $table = 'responsibilities';
    protected $fillable = [
        'code',
    ];

    public $timestamps = false;

    //Управления
    public const MANAGER_ORDER = 1001; // order
    public const MANAGER_PRODUCT = 1002; //product
    public const MANAGER_ACCOUNTING = 1003; //accounting
    public const MANAGER_DELIVERY = 1004; //delivery

    public const MANAGER_LOGGER = 1005; //logger
    public const MANAGER_DISCOUNT = 1006; //discount
    public const MANAGER_USER = 1007; //user
    public const MANAGER_PAYMENT = 1008; //paid
    public const MANAGER_STAFF = 1009; //staff


    public const MANAGER_OPTIONS = 1010; //options
    public const MANAGER_PRICING = 1011; //pricing
    public const MANAGER_REFUND = 1012; //refund

    public const MANAGER_SUPPLY = 1013; //supply

    //Отчеты и/или Контроль
    public const REPORT_THROWABLE = 2001;
    public const REPORT_OTHER = 2002;
    //TODO прописать все обязанности
    // и права, например, получать отчеты

    const RESPONSE = [
        self::MANAGER_ORDER => 'Работа с заказами',
        self::MANAGER_PRODUCT => 'Товары, категории, атрибуты',
        self::MANAGER_ACCOUNTING => 'Товарный учет',
        self::MANAGER_DELIVERY => 'Доставка товаров',
        self::MANAGER_LOGGER => 'Сборка и выдача товаров',
        self::MANAGER_DISCOUNT => 'Работа со скидками',
        self::MANAGER_PAYMENT => 'Работа с платежами',
        self::MANAGER_PRICING => 'Работа с ценам',
        self::MANAGER_USER => 'Доступ к данным о клиенте',
        self::MANAGER_STAFF => 'Доступ к данным о сотруднике',
        self::MANAGER_OPTIONS => 'Настройки сайта',
        self::MANAGER_REFUND => 'Работа с возвратами',
        self::MANAGER_SUPPLY => 'Заказ товаров',
        // ----------------------- //
        self::REPORT_THROWABLE => 'Report: Логи по ошибкам сайта',
        self::REPORT_OTHER => 'Report: Другие отчеты',

    ];

    public static function new(int $resp): self
    {
        return self::make(['code' => $resp]);
    }
}
