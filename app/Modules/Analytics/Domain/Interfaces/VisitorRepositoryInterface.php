<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Domain\Interfaces;

use App\Modules\Analytics\Domain\Entities\VisitorEntity;
use App\Modules\Analytics\Domain\ValueObjects\VisitorUuid;
use DateTimeImmutable;

interface VisitorRepositoryInterface
{
    /** Найти посетителя по внутреннему ID. */
    public function findById(int $id): ?VisitorEntity;

    /** Найти по UUID из cookie. */
    public function findByUuid(VisitorUuid $uuid): ?VisitorEntity;

    /** Найти по client_id (для связки/канонизации). */
    public function findByClientId(int $clientId): ?VisitorEntity;

    /** Создать нового посетителя. Возвращает сущность с присвоенным ID. */
    public function create(VisitorEntity $visitor): VisitorEntity;

    /** Обновить существующего (last_visit_at, visits_count, client_id, uuid и т.д.). */
    public function update(VisitorEntity $visitor): void;

    /** Удалить посетителя (при канонизации дубликата). */
    public function delete(int $id): void;

    /** Обновить только uuid (при потере cookie). */
    public function changeUuid(int $visitorId, VisitorUuid $newUuid): void;

    /** Обновить только client_id (при логине). */
    public function linkToClient(int $visitorId, int $clientId, DateTimeImmutable $linkedAt): void;

    /** Инкрементировать visits_count и last_visit_at без полной загрузки. */
    public function touchVisit(int $visitorId, DateTimeImmutable $visitedAt): void;

    /** Записать гео-данные (асинхронно после GeoIP). */
    public function setGeoData(int $visitorId, ?string $city, ?string $region, ?string $country): void;

    /**
     * Найти «спящих» посетителей (для маркетинга): last_visit_at < $before.
     *
     * @return VisitorEntity[]
     */
    public function findInactiveSince(DateTimeImmutable $before, int $limit = 100): array;

    /**
     * Посетители, у которых client_id появился в диапазоне дат (для воронок).
     *
     * @return VisitorEntity[]
     */
    public function findRegisteredBetween(DateTimeImmutable $from, DateTimeImmutable $to): array;
}
