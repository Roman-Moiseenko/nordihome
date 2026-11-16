<?php

namespace App\Modules\Marketplace\Application\Services\Avito;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Единая точка доступа к API Авито.
 *
 * Содержит все HTTP-методы модуля: авторизация (токен), дерево категорий,
 * параметры категории и получение avito_id по внутреннему ad_id.
 */
class AvitoAPIService
{
    public function __construct(
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly string $userId,
    ) {}

    /**
     * Получение access-токена (client_credentials), кэшируется на 23 часа.
     */
    public function getToken(): string
    {
        return Cache::remember('avito_api_token', now()->addHours(23), function () {
            $response = Http::asForm()->post('https://api.avito.ru/token', [
                'grant_type'    => 'client_credentials',
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);

            if ($response->failed()) {
                throw new RuntimeException('Avito auth failed: ' . $response->body());
            }

            return $response->json('access_token');
        });
    }

    /**
     * Возвращает массив корневых узлов дерева категорий (ключ "categories" ответа Авито).
     *
     * @return array<int, array{name: string, slug: string, nested: array|string}>
     */
    public function fetchCategoryTree(): array
    {
        $response = Http::withToken($this->getToken())
            ->get('https://api.avito.ru/autoload/v1/user-docs/tree');

        if ($response->failed()) {
            throw new RuntimeException('Avito categories fetch failed: ' . $response->body());
        }

        return $response->json('categories') ?? [];
    }

    /**
     * Получить поля для конкретной категории по её slug.
     *
     * @param string $categorySlug Slug категории из autoload_user_docs_tree
     * @return array
     */
    public function fetchFields(string $categorySlug): array
    {
        $response = Http::withToken($this->getToken())
            ->get("https://api.avito.ru/autoload/v1/user/docs/node/{$categorySlug}/fields");

        if ($response->failed()) {
            throw new RuntimeException(
                "Avito fields fetch failed for '{$categorySlug}': " . $response->body()
            );
        }

        return $response->json();
    }

    /**
     * Возвращает внешний avito_id объявления по внутреннему ad_id.
     */
    public function getAvitoIdByAdId(string $adId): ?string
    {
        $token = $this->getToken();

        $response = Http::withToken($token)
            ->get("https://api.avito.ru/autoload/v1/accounts/{$this->userId}/items/{$adId}/");

        if ($response->failed()) {
            // Логируем ошибку
            Log::warning('Avito getAvitoIdByAdId failed', [
                'ad_id' => $adId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        return $response->json('avito_id');
    }
}
