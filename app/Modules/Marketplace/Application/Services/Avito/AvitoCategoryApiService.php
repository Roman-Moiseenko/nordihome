<?php

namespace App\Modules\Marketplace\Application\Services\Avito;

use Illuminate\Support\Facades\Http;

class AvitoCategoryApiService
{
    public function __construct(
        private readonly AvitoAuthService $authService,
    ) {}

    /**
     * Возвращает плоский массив узлов дерева категорий.
     *
     * @return array<int, array{slug: string, name: string, parent_slug: ?string, children: array}>
     */
    public function fetchCategoryTree(): array
    {
        $response = Http::withToken($this->authService->getToken())
            ->get('https://api.avito.ru/autoload/v1/user/docs/tree');

        if ($response->failed()) {
            throw new \RuntimeException('Avito categories fetch failed: ' . $response->body());
        }

        return $response->json();
    }
}
