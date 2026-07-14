<?php

namespace App\Modules\Shop\Application\Interfaces;

interface BreadcrumbProviderInterface
{
    /**
     * Возвращает массив хлебных крошек.
     * Каждый элемент: ['name' => '...', 'url' => '...']
     *
     * @param string $routeName   Имя маршрута (например 'shop.category.view')
     * @param array  $params      Параметры маршрута
     * @return array
     */
    public function generate(string $routeName, array $params = []): array;
}
