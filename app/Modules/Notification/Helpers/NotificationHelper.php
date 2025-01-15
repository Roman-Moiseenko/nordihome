<?php
declare(strict_types=1);

namespace App\Modules\Notification\Helpers;

/**
 * Тип отправляемого уведомления, используется для текстового и графического обозначения
 * сообщений в чат-боте телеграма.
 */
class NotificationHelper
{

    const EVENT_TEST = 1001;
    const EVENT_ERROR = 1002;
    const EVENT_CHIEF = 1003;
    const EVENT_INFO = 1004;

    const EVENT_NEW_ORDER = 1010;
    const EVENT_ORDER_RECEIVED = 1011;
    const EVENT_ORDER_CANCEL = 1012;
    const EVENT_ORDER_CONFIRM = 1013;

    const EVENT_PAYMENT = 1014;

    const EVENT_ASSEMBLY = 1021;

    const EVENT_DELIVERY = 1023;

    const EVENTS = [
        self::EVENT_TEST => 'Тестовое событие',
        self::EVENT_ERROR => 'Ошибка',
        self::EVENT_CHIEF => 'От руководства',
        self::EVENT_INFO => 'Информирование',
        self::EVENT_NEW_ORDER => 'Новый заказ',
        self::EVENT_ORDER_RECEIVED => 'Заказ получен',
        self::EVENT_ORDER_CANCEL => 'Заказ отменен',
        self::EVENT_ORDER_CONFIRM => 'Заказ подтвержден',
        self::EVENT_PAYMENT => 'Поступил платеж',
        self::EVENT_ASSEMBLY => 'Собрать товар',
        self::EVENT_DELIVERY => 'Доставить товар',

    ];

    /**
     * Смайлики для обозначения Событий
     * https://apps.timwhitlock.info/emoji/tables/unicode
     */
    const EMOJI = [
        self::EVENT_TEST => "\xE2\x9C\x94", //✔
        self::EVENT_ERROR => "\xE2\x9D\x8C",
        self::EVENT_CHIEF => "\xF0\x9F\x93\xA3",
        self::EVENT_INFO => "\xE2\x84\xB9",
        self::EVENT_NEW_ORDER => "\xF0\x9F\x93\x8B", //📋  "\xF0\x9F\x86\x95" 🆕
        self::EVENT_ORDER_RECEIVED => "\xE2\x9C\x85", //✅
        self::EVENT_ORDER_CANCEL => "\xE2\x9D\x8C", //❌
        self::EVENT_ORDER_CONFIRM => "\xF0\x9F\x86\x97", //✅🆗
        self::EVENT_PAYMENT => "\xF0\x9F\x92\xB0", //💰
        self::EVENT_ASSEMBLY => "\xF0\x9F\x93\xA6", //📦
        self::EVENT_DELIVERY => "\xF0\x9F\x9A\x9A", //🚚
    ];


}
