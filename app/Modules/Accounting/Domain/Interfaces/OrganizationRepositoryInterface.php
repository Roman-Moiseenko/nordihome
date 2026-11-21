<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Domain\Interfaces;

use App\Modules\Accounting\Application\DTOs\Organization\FilterOrganizationIndexData;
use App\Modules\Accounting\Application\DTOs\Organization\OrganizationContactData;
use App\Modules\Accounting\Domain\Entities\OrganizationContactEntity;
use App\Modules\Accounting\Domain\Entities\OrganizationEntity;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;

interface OrganizationRepositoryInterface
{
    public function getById(int $id): OrganizationEntity;

    public function save(OrganizationEntity $organization): OrganizationEntity;

    public function delete(int $id): void;

    public function findByInn(string $inn): ?OrganizationEntity;

    /**
     * Поиск для выпадающего списка (ИНН / название).
     *
     * @return array<int, array{id: int, short_name: string, inn: string}>
     */
    public function search(string $value): array;

    /**
     * @return LengthAwarePaginator<OrganizationEntity>
     */
    public function filterPaginate(FilterOrganizationIndexData &$filter): LengthAwarePaginator;

    public function saveContact(int $organizationId, OrganizationContactData $data): OrganizationContactEntity;

    public function deleteContact(int $contactId): void;

    /**
     * Список документов организации для карточки.
     *
     * @return array<int, array{id: int, url: string, title: string, type: string, file: string}>
     */
    public function getContracts(int $organizationId): array;

    /**
     * @return array<int, array{id: int, url: string, title: string, type: string, file: string}>
     */
    public function getDocuments(int $organizationId): array;

    public function attachFile(int $organizationId, UploadedFile $file, string $type, string $title = ''): void;

    public function getTypes(int $organizationId): string;

    /**
     * @return array{id: int, name: string}|null
     */
    public function getTraderLink(int $organizationId): ?array;

    /**
     * @return array{id: int, name: string}|null
     */
    public function getDistributorLink(int $organizationId): ?array;

    /**
     * @return array{id: int, fullname: string}|null
     */
    public function getShopperLink(int $organizationId): ?array;
}
