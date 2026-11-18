<?php

namespace App\Modules\Marketplace\Application\Mappers;

/**
 * Поля сущности AvitoProductEntity, доступные для сопоставления в реестре
 * параметров (вид "product" — индивидуальное поле товара Авито).
 *
 * Ключ — имя свойства сущности, значение — подпись для выпадающего списка
 * на странице реестра.
 */
final class AvitoProductFields
{
    /**
     * @var array<string, string> field => label
     */
    public const OPTIONS = [
        'adId' => 'adId — Уникальный идентификатор объявления',
        'adNumber' => 'adNumber — Номер объявления на Авито',
        'productId' => 'productId — Связь с товаром каталога',
        'avitoCategoryId' => 'avitoCategoryId — Категория Авито',
        'name' => 'name — Название объявления',
        'description' => 'description — Описание объявления',
        'basePrice' => 'basePrice — Базовая цена',
        'sellPrice' => 'sellPrice — Цена продажи',
        'pricePackage' => 'pricePackage — Цена в упаковке',
        'tnVed' => 'tnVed — ТН ВЭД',
        'okpd2' => 'okpd2 — ОКПД 2',
        'country' => 'country — Страна происхождения',
        'delivery' => 'delivery — Автодоставка',
        'inStock' => 'inStock — Доступность',
        'active' => 'active — Активность/архив',
    ];

    /**
     * @return array<string, string> field => label
     */
    public static function options(): array
    {
        return self::OPTIONS;
    }

    public static function has(string $field): bool
    {
        return array_key_exists($field, self::OPTIONS);
    }
}
