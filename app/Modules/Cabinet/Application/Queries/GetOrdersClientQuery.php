<?php

namespace App\Modules\Cabinet\Application\Queries;

use App\Modules\Cabinet\Application\Actions\GetOrderClientData;
use App\Modules\Cabinet\Application\DTOs\OrdersClientPageData;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;
use App\Modules\Shop\Infrastructure\Persistence\Builders\PaginatorBuilder;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class GetOrdersClientQuery
{
    public function __construct(
        private OrderRepositoryInterface $repository,
        private PaginatorBuilder            $paginatorBuilder,
        private GetOrderClientData $getOrderClientData,
    )
    {
    }

    public function execute(int $clientId, array $params): OrdersClientPageData
    {
        $perPage = 10;
        $page = (int)($params['page'] ?? 1);

        /** @var LengthAwarePaginator<int> $idsPaginator */
        $idsPaginator = $this->repository->getIdsByClientId($clientId, $perPage, $page);

        $orders = array_map(
            fn(int $id) => $this->getOrderClientData->execute($id),
            $idsPaginator->items(),
        );

        $paginator = $this->paginatorBuilder->build(
            total: $idsPaginator->total(),
            perPage: $perPage,
            currentPage: $page,
            options: [
                'path' => '/' . request()->path(),
                'query' => array_diff_key(request()->query(), ['page' => null]),
            ]
        );

        return new OrdersClientPageData(
            orders: $orders,
            paginator: $paginator,
            meta: new SeoData('Мои Заказы', ''),
        );
    }
}
