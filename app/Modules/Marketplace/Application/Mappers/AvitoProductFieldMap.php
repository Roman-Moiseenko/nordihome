<?php

declare(strict_types=1);

namespace App\Modules\Marketplace\Application\Mappers;

/**
 * Сопоставление русских названий колонок выгрузки Авито с полями AvitoProduct.
 *
 * Поиск параметров при загрузке идёт по русскому названию колонки:
 *   1) поле товара (MAP) — пишется прямо в сущность AvitoProduct;
 *   2) общий параметр AvitoParameter — пропускается (не сохраняется);
 *   3) параметр категории AvitoCategoryField — сохраняется в avito_product_values;
 *   иначе — параметр не найден (пишется в лог).
 */
final class AvitoProductFieldMap
{
    /**
     * Русское название колонки => имя свойства сущности AvitoProductEntity.
     *
     * @var array<string, string>
     */
    public const MAP = [
        'Уникальный идентификатор объявления' => 'adId',
        'Номер объявления на Авито' => 'adNumber',
        'Название объявления' => 'name',
        'Описание объявления' => 'description',
        'Цена' => 'sellPrice',
        'Ссылки на фото' => 'avitoImages',
        'Способ доставки' => 'delivery',
        'ТН ВЭД' => 'tnVed',
        'ОКПД 2' => 'okpd2',
        'Страна-производитель' => 'country',
        'Страна происхождения' => 'country',
        'Страна происхождения товара' => 'country',
        'Доступность' => 'inStock',
    ];

    /**
     * Колонки, которые не имеют соответствия и молча пропускаются.
     *
     * @var list<string>
     */
    public const SKIP = [
        'Id',
        'Идентификатор',
        'Категория',
        'Широта',
        'Долгота',
        'Адрес',
        'Номер телефона',
        'Названия фото',
        'Вид товара',
        'Почта',
        'Название компании',
        'AvitoDateEnd',
        'AvitoStatus',
        'Вид товара',
        'WholesaleTypePredict2Param',
        'Названия фото',
    ];

    public static function fieldByLabel(string $label): ?string
    {
        return self::MAP[trim($label)] ?? null;
    }

    public static function isSkip(string $label): bool
    {
        return in_array(trim($label), self::SKIP, true);
    }
}
