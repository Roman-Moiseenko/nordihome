<?php

namespace App\Modules\Auth\Application\Actions\Client;

use App\Modules\Auth\Domain\Interfaces\ClientRepositoryInterface;
use App\Modules\Auth\Domain\ValueObjects\NewsletterConsent;
use InvalidArgumentException;

readonly class NewsletterConsentClientUseCase
{
    public function __construct(private ClientRepositoryInterface $clientRepository)
    {
    }

    public function execute(
        ?int $clientId,
        string $source = NewsletterConsent::SOURCE_POPUP,
        ?string $actionIdentifier = null,
    ): void
    {
        if (is_null($clientId)) throw new InvalidArgumentException('Нет id Client');
        $client = $this->clientRepository->findById($clientId);

        $consent = new NewsletterConsent(
            consentTextVersion: '№2 от 01.01.2026',
            source: $source,
            actionIdentifier: $actionIdentifier,
        );
        $client->newsletterConsent = $consent;
        $this->clientRepository->save($client);
    }
}
