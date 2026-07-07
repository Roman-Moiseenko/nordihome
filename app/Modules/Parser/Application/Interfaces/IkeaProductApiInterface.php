<?php

namespace App\Modules\Parser\Application\Interfaces;

interface IkeaProductApiInterface
{
    /**
     * Получить список товаров по категории (с внутренней пагинацией)
     * @return array{products: array, total: int}
     */
    public function getProductsByCategory(string $ikeaId): array;

    /**
     * Получить данные товара по коду
     * @return array|null массив product из ответа API, или null если не найден
     */
    public function getProductByCode(string $code): ?array;

    /**
     * Получить и распарсить страницу товара
     * @return array|null массив pageProps.product из HTML страницы
     */
    public function getProductPage(string $pipUrl): ?array;

    /**
     * Получить остатки товара по коду
     * @return array|null массив availabilities из ответа API
     */
    public function getAvailability(string $code): ?array;
}
