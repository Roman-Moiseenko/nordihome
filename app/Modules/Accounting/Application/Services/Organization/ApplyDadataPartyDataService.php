<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Services\Organization;

use App\Modules\Accounting\Domain\Entities\OrganizationEntity;
use App\Modules\Accounting\Domain\ValueObjects\Geo;
use App\Modules\Auth\Domain\ValueObjects\Address;
use App\Modules\Auth\Domain\ValueObjects\FullName;

/**
 * Применяет данные, полученные из Dadata, к сущности организации.
 */
class ApplyDadataPartyDataService
{
    /**
     * @param array<string, mixed> $data
     */
    public function apply(OrganizationEntity $entity, array $data): void
    {
        $entity->shortName = $data['name']['short_with_opf'];
        $entity->fullName = $data['name']['full_with_opf'];
        $entity->kpp = (string) ($data['kpp'] ?? '');
        $entity->ogrn = (string) ($data['ogrn'] ?? '');

        $address = $data['address']['data'] ?? [];
        $entity->legalAddress = (string) ($address['source'] ?? '');
        if ($entity->actualAddress->getFullAddress() == '') {
            $entity->actualAddress = new Address(
                country: $address['country'] ?? null,
                city: $address['city'] ?? null,
                street: $address['street_with_type'] ?? null,
                region: $address['region_with_type'] ?? null,
                postalCode: $address['postal_code'] ?? null,
                house: $address['house'] ?? null,
                building: $address['block'] ?? null,
                flat: $address['flat'] ?? null,
            );
        }
        $entity->geo = new Geo(
            latitude: (string) ($address['geo_lat'] ?? ''),
            longitude: (string) ($address['geo_lon'] ?? ''),
        );

        if (($data['type'] ?? '') === 'INDIVIDUAL') {
            $entity->post = 'Индивидуальный предприниматель';
            $fio = $data['fio'] ?? [];
            $entity->chief = new FullName(trim(implode(' ', array_filter([
                (string) ($fio['surname'] ?? ''),
                (string) ($fio['name'] ?? ''),
                (string) ($fio['patronymic'] ?? ''),
            ]))));
        } else {
            $management = $data['management'] ?? [];
            $entity->post = (string) ($management['post'] ?? '');
            $entity->chief = new FullName((string) ($management['name'] ?? ''));
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    public function applyBank(OrganizationEntity $entity, array $data): void
    {
        $entity->bik = (string) ($data['bic'] ?? '');
        $entity->bankName = (string) ($data['name']['payment'] ?? '');
        $entity->corrAccount = (string) ($data['correspondent_account'] ?? '');
    }
}
