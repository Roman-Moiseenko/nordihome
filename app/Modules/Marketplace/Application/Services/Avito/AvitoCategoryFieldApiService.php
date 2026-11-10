<?php

namespace App\Modules\Marketplace\Application\Services\Avito;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class AvitoCategoryFieldApiService
{
    public function __construct(
        private readonly AvitoAuthService $authService,
    ) {}
    /**
     * Получить поля для конкретной категории по её slug.
     *
     * @param string $categorySlug Slug категории из autoload_user_docs_tree
     * @return array
     */
    public function fetchFields(string $categorySlug): array
    {
        $response = Http::withToken($this->authService->getToken())
            ->get("https://api.avito.ru/autoload/v1/user/docs/node/{$categorySlug}/fields");

        if ($response->failed()) {
            throw new RuntimeException(
                "Avito fields fetch failed for '{$categorySlug}': " . $response->body()
            );
        }

        return $response->json();
    }
}
