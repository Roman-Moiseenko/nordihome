<?php

namespace App\Modules\Shop\Infrastructure\Persistence\Builders;

use App\Modules\Shop\Application\DTOs\Parts\PaginatorData;

class PaginatorBuilder
{
    /**
     * @param array $options ассоциативный массив с ключами:
     *   - 'path' (string) - базовый URL (без query string)
     *   - 'query' (array) - текущие параметры запроса (кроме 'page')
     * @return PaginatorData
     */
    public function build(int $total, int $perPage, int $currentPage, array $options): PaginatorData
    {
        $lastPage = (int) ceil($total / max($perPage, 1));
        $page = max(1, min($currentPage, $lastPage));

        // Генерация URL для каждой страницы
        $urls = [];
        $basePath = $options['path'] ?? '';
        $baseQuery = $options['query'] ?? [];
        for ($i = 1; $i <= $lastPage; $i++) {
            $query = array_merge($baseQuery, ['page' => $i]);
            $urls[$i] = $basePath . '?' . http_build_query($query);
        }

        // Построение элементов пагинации
        $elements = [];
        if ($lastPage <= 9) {
            $elements[] = array_slice($urls, 0, null, true);
        } else {
            $window = 2; // радиус окна вокруг текущей страницы
            $sliderStart = max(2, $page - $window);
            $sliderEnd = min($lastPage - 1, $page + $window);

            // Первая страница
            $elements[] = [1 => $urls[1]];

            // Многоточие перед окном, если нужно
            if ($sliderStart > 2) {
                $elements[] = '...';
            }

            // Окно страниц
            $range = [];
            for ($i = $sliderStart; $i <= $sliderEnd; $i++) {
                $range[$i] = $urls[$i];
            }
            $elements[] = $range;

            // Многоточие после окна, если нужно
            if ($sliderEnd < $lastPage - 1) {
                $elements[] = '...';
            }

            // Последняя страница
            $elements[] = [$lastPage => $urls[$lastPage]];
        }

        return new PaginatorData(
            total: $total,
            perPage: $perPage,
            currentPage: $page,
            lastPage: $lastPage,
            hasPages: $lastPage > 1,
            onFirstPage: $page <= 1,
            hasMorePages: $page < $lastPage,
            elements: $elements,
            url: $urls,
            previousPageUrl: $page > 1 ? $urls[$page - 1] : null,
            nextPageUrl: $page < $lastPage ? $urls[$page + 1] : null,
        );
    }
}
