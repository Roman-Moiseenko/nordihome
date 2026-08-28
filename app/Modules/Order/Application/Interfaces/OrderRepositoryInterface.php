<?php
declare(strict_types=1);

namespace App\Modules\Order\Application\Interfaces;

use App\Modules\Order\Application\DTOs\Order\FilterOrderIndexData;
use App\Modules\Order\Domain\Entities\OrderEntity;
use Illuminate\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    /** @return OrderEntity[] */
    public function getAll(): array;

    public function getById(int $id): OrderEntity;

    public function save(OrderEntity $order): OrderEntity;

    public function delete(int $id): void;

    /** @return LengthAwarePaginator<OrderEntity> */
    public function getAllPaginated(int $perPage = 15, int $page = 1): LengthAwarePaginator;

    /** @return LengthAwarePaginator<OrderEntity> */
    public function getByClientId(int $clientId, int $perPage = 15, int $page = 1): LengthAwarePaginator;
    /** @return LengthAwarePaginator<int> */
    public function getIdsByClientId(int $clientId, int $perPage = 15, int $page = 1): LengthAwarePaginator;

    /** @return LengthAwarePaginator<OrderEntity> */
    public function getFilteredPaginated(FilterOrderIndexData &$filter): LengthAwarePaginator;
}
