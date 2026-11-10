<?php

namespace App\Modules\Marketplace\Application\Services\Avito;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class AvitoAuthService
{
    public function __construct(
        private readonly string $clientId,
        private readonly string $clientSecret,
    ) {}
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
}
