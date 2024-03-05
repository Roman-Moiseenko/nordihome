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
    public const MANAGER_ORDER = 1001;
    public const MANAGER_PRODUCT = 1002;
    public const MANAGER_ACCOUNTING = 1003;
    public const MANAGER_DELIVERY = 1004;

    public const MANAGER_LOGGER = 1005;
    public const MANAGER_DISCOUNT = 1006;
    public const MANAGER_USER = 1007;


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
        self::MANAGER_USER => 'Доступ к данным о клиенте',
        // ----------------------- //
        self::REPORT_THROWABLE => 'Логи по ошибкам сайта',
        self::REPORT_OTHER => 'Другие отчеты',

    ];

    public static function new(int $resp): self
    {
        return self::make(['code' => $resp]);
    }
}
