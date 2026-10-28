<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Domain\Interfaces;

use DateTimeImmutable;

/**
 * AnalyticsReportRepositoryInterface — read-only контракт для отчётов аналитики.
 *
 * В отличие от репозиториев, которые работают с Domain Entity (создание/обновление),
 * этот интерфейс отдаёт готовые структуры для Inertia: ассоциативные массивы
 * (read-модели). Здесь не нужно гидратировать сущности — отчёты считаются напрямую
 * из append-only таблиц analytics_* и агрегатов.
 */
interface AnalyticsReportRepositoryInterface
{
    /** Сводка для дашборда: totals, daily, devices, sources. */
    public function dashboard(DateTimeImmutable $from, DateTimeImmutable $to): array;

    /** Отчёт по поиску: топ, без результатов, низкий CTR, средняя позиция. */
    public function search(DateTimeImmutable $from, DateTimeImmutable $to): array;

    /** Отчёт по просмотрам страниц: топ, метрики, входы/выходы. */
    public function pages(DateTimeImmutable $from, DateTimeImmutable $to): array;

    /** Отчёт по действиям клиентов: totals по типам, корзина, избранное. */
    public function actions(DateTimeImmutable $from, DateTimeImmutable $to): array;

    /** Отчёт по источникам: sources, utm, конверсия. */
    public function sources(DateTimeImmutable $from, DateTimeImmutable $to): array;

    /** Отчёт по аудитории: география, устройства, OS/браузер, новые/вернувшиеся. */
    public function audience(DateTimeImmutable $from, DateTimeImmutable $to): array;

    /** Отчёт по воронке: топ путей, этапы воронки, брошенные корзины. */
    public function funnel(DateTimeImmutable $from, DateTimeImmutable $to): array;
}
