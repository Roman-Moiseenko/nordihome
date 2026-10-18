<?php

declare(strict_types=1);

namespace App\Modules\Order\Application\Services;

use App\Modules\Auth\Domain\Interfaces\ClientRepositoryInterface;
use App\Modules\Order\Domain\Exceptions\ClientOrderDataIncompleteException;

/**
 * Проверяет полноту данных клиента, необходимых для оформления заказа.
 */
readonly class ClientOrderRequirementsChecker
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository,
    ) {}

    /**
     * @throws ClientOrderDataIncompleteException
     */
    public function check(?int $clientId): void
    {
        $client = $this->clientRepository->findById($clientId);

        if ($client === null) {
            throw new ClientOrderDataIncompleteException(['client']);
        }

        $missing = [];

        if (trim($client->fullName->getLastName()) === '' || trim($client->fullName->getFirstName()) === '') {
            $missing[] = 'Фамилию и Имя';
        }

        if (!$client->isPickup) {
            if ($client->phone === null || $client->phone->getValue() === '') {
                $missing[] = 'Телефон';
            }

            if ($client->address === null || empty($client->address->regionCode)) {
                $missing[] = 'Регион';
            }
        }

        if (!empty($missing)) {
            throw new ClientOrderDataIncompleteException($missing);
        }
    }
}
