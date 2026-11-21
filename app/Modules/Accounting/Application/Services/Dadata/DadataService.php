<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Services\Dadata;

use Dadata\DadataClient;

/**
 * Обёртка над DadataClient. Токен и секрет берутся из config('shop.dadata').
 */
class DadataService
{
    public function findPartyByInn(string $inn): array
    {
        $response = $this->client()->findById('party', $inn);

        if (empty($response)) {
            throw new \DomainException('Неверный ИНН. Организация не найдена');
        }

        return $response[0]['data'] ?? [];
    }

    public function findBankByBik(string $bik): array
    {
        $response = $this->client()->findById('bank', $bik);

        return $response[0]['data'] ?? [];
    }

    private function client(): DadataClient
    {
        $token = (string) config('shop.dadata.token', '');
        $secret = (string) config('shop.dadata.secret', '');

        return new DadataClient($token, $secret);
    }
}
