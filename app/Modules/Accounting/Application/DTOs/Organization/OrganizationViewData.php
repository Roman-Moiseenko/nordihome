<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\DTOs\Organization;

use App\Modules\Accounting\Domain\Entities\OrganizationEntity;
use Spatie\LaravelData\Data;

class OrganizationViewData extends Data
{
    /**
     * @param array<int, array<string, mixed>> $contacts
     * @param array<int, array<string, mixed>> $contracts
     * @param array<int, array<string, mixed>> $documents
     * @param array<string, mixed>|null $trader
     * @param array<string, mixed>|null $distributor
     * @param array<string, mixed>|null $shopper
     */
    public function __construct(
        public readonly int $id,
        public readonly string $fullName,
        public readonly string $shortName,
        public readonly string $inn,
        public readonly string $kpp,
        public readonly string $ogrn,
        public readonly bool $foreign,
        public readonly string $bik,
        public readonly string $bankName,
        public readonly string $corrAccount,
        public readonly string $payAccount,
        public readonly ?int $vatId,
        public readonly string $vatCaption,
        public readonly ?string $email,
        public readonly string $phone,
        public readonly string $post,
        public readonly bool $active,
        public readonly string $legalAddress,
        public readonly array $actualAddress,
        public readonly string $actualAddressFull,
        public readonly array $geo,
        public readonly string $chief,
        public readonly array $contacts,
        public readonly array $contracts,
        public readonly array $documents,
        public readonly ?array $trader,
        public readonly ?array $distributor,
        public readonly ?array $shopper,
    ) {
    }

    public static function fromEntity(
        OrganizationEntity $organization,
        array $contracts = [],
        array $documents = [],
        ?array $trader = null,
        ?array $distributor = null,
        ?array $shopper = null,
        string $vatCaption = '',
    ): self {
        return new self(
            id: $organization->id,
            fullName: $organization->fullName,
            shortName: $organization->shortName,
            inn: $organization->inn,
            kpp: $organization->kpp,
            ogrn: $organization->ogrn,
            foreign: $organization->foreign,
            bik: $organization->bik,
            bankName: $organization->bankName,
            corrAccount: $organization->corrAccount,
            payAccount: $organization->payAccount,
            vatId: $organization->vatId,
            vatCaption: $vatCaption,
            email: $organization->email?->value,
            phone: $organization->phone->getValue(),
            post: $organization->post,
            active: $organization->active,
            legalAddress: $organization->legalAddress,
            actualAddress: [
                'country' => $organization->actualAddress->country,
                'region' => $organization->actualAddress->region,
                'city' => $organization->actualAddress->city,
                'street' => $organization->actualAddress->street,
                'house' => $organization->actualAddress->house,
                'building' => $organization->actualAddress->building,
                'flat' => $organization->actualAddress->flat,
                'postalCode' => $organization->actualAddress->postalCode,
            ],
            actualAddressFull: $organization->actualAddress->getFullAddress(),
            geo: $organization->geo->toArray(),
            chief: (string) $organization->chief,
            contacts: array_map(
                fn($contact) => [
                    'id' => $contact->id,
                    'post' => $contact->post,
                    'phone' => $contact->phone->getValue(),
                    'email' => $contact->email?->value,
                    'fullname' => (string) $contact->fullName,
                ],
                $organization->contacts
            ),
            contracts: $contracts,
            documents: $documents,
            trader: $trader,
            distributor: $distributor,
            shopper: $shopper,
        );
    }
}
