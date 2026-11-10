<?php

namespace App\Modules\Marketplace\Application\Services\Avito;

use Illuminate\Support\Facades\Http;

class AvitoCategoryApiService
{
    public function __construct(
        private readonly AvitoAuthService $authService,
    ) {}

    /**
     * Возвращает массив корневых узлов дерева категорий (ключ "categories" ответа Авито).
     *
     * @return array<int, array{name: string, slug: string, nested: array|string}>
     */
    public function fetchCategoryTree(): array
    {
        $response = Http::withToken($this->authService->getToken())
            ->get('https://api.avito.ru/autoload/v1/user-docs/tree');

        if ($response->failed()) {
            throw new \RuntimeException('Avito categories fetch failed: ' . $response->body());
        }

        return $response->json('categories') ?? [];
    }
}
