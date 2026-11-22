<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Actions\Organization;

use App\Modules\Accounting\Application\DTOs\Organization\OrganizationSchemaData;
use App\Modules\Accounting\Domain\Entities\TraderEntity;
use App\Modules\Accounting\Domain\Interfaces\OrganizationRepositoryInterface;
use App\Modules\Accounting\Domain\Interfaces\TraderRepositoryInterface;
use App\Modules\Content\Domain\Entities\ContactEntity;
use App\Modules\Content\Domain\Interfaces\ContactRepositoryInterface;
use App\Modules\Content\Domain\ValueObjects\ContactChannel;
use App\Modules\Shared\Infrastructure\Services\PhotoService;

readonly class GetSellerOrganizationQuery
{
    public function __construct(
        private TraderRepositoryInterface $traderRepository,
        private OrganizationRepositoryInterface $organizationRepository,
        private ContactRepositoryInterface $contactRepository,
        private PhotoService $photoService,
    ) {
    }

    public function execute(): ?OrganizationSchemaData
    {
        $trader = $this->traderRepository->getDefault();
        if ($trader === null) {
            return null;
        }

        $organizationId = $this->resolveDefaultOrganizationId($trader);
        if ($organizationId === null) {
            return null;
        }

        $organization = $this->organizationRepository->getById($organizationId);

        [$sameAs, $phones] = $this->resolveContacts();

        return new OrganizationSchemaData(
            name: $organization->shortName,
            address: $organization->actualAddress,
            url: $trader->url,
            logoUrl: $this->resolveLogoUrl($trader),
            logoWidth: $trader->logo?->getWidth() ?? 0,
            logoHeight: $trader->logo?->getHeight() ?? 0,
            logoCaption: $trader->name,
            sameAs: $sameAs,
            phones: $phones,
            traderName: $trader->name,
        );
    }

    private function resolveLogoUrl(TraderEntity $trader): string
    {
        if ($trader->logo === null || $trader->logo->getId() === null) {
            return '';
        }

        return $this->photoService->getUploadUrlById((int) $trader->logo->getId());
    }

    private function resolveDefaultOrganizationId(TraderEntity $trader): ?int
    {
        foreach ($trader->organizations as $organization) {
            if ($organization->default) {
                return $organization->organizationId;
            }
        }

        return $trader->organizations[0]->organizationId ?? null;
    }

    /**
     * @return array{0: string[], 1: string[]}
     */
    private function resolveContacts(): array
    {
        $sameAs = [];
        $phones = [];

        foreach ($this->contactRepository->getAll() as $contact) {
            if (!$contact->isPublished()) {
                continue;
            }

            if ($contact->channel?->equals(ContactChannel::phone())) {
                $this->appendPhone($phones, $contact);
                continue;
            }

            $url = trim($contact->url);
            if ($url !== '' && $this->isHttpUrl($url)) {
                $sameAs[] = $url;
            }
        }

        return [
            array_values(array_unique($sameAs)),
            array_values(array_unique($phones)),
        ];
    }

    /**
     * @param string[] $phones
     */
    private function appendPhone(array &$phones, ContactEntity $contact): void
    {
        $url = trim($contact->url);
        if (!str_starts_with($url, 'tel:')) {
            return;
        }

        $phone = trim(substr($url, 4));
        if ($phone !== '') {
            $phones[] = $phone;
        }
    }

    private function isHttpUrl(string $url): bool
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https'], true);
    }
}
