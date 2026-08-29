<?php

declare(strict_types=1);

namespace App\Modules\Discount\Presentation\Http\Controllers\Web;

use App\Modules\Discount\Application\Actions\PromotionProduct\AssignProductsToPromotionUseCase;
use App\Modules\Discount\Application\Actions\PromotionProduct\AttachProductsToPromotionUseCase;
use App\Modules\Discount\Application\Actions\PromotionProduct\DetachProductsFromPromotionUseCase;
use App\Modules\Discount\Application\Actions\PromotionProduct\ListProductByPromotionUseCase;
use App\Modules\Discount\Application\Actions\PromotionProduct\SetProductPriceUseCase;
use App\Modules\Discount\Application\DTOs\PromotionProduct\PromotionProductPriceData;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

readonly class PromotionProductController
{
    public function __construct(
        private ListProductByPromotionUseCase    $listProductByPromotionUseCase,
        private AssignProductsToPromotionUseCase $assignProductsToPromotionUseCase,
        private AttachProductsToPromotionUseCase $attachProductsToPromotionUseCase,
        private DetachProductsFromPromotionUseCase $detachProductsFromPromotionUseCase,
        private SetProductPriceUseCase           $setProductPriceUseCase,
    )
    {
    }

    /**
     * Список товаров акции (с пагинацией).
     * GET /admin/discount/promotion/{id}/products
     */
    public function promotionProducts(int $id, Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 15);
        $page = $request->integer('page', 1);

        $paginator = $this->listProductByPromotionUseCase->execute($id, $perPage, $page);

        return response()->json($paginator, Response::HTTP_OK);
    }

    /**
     * Назначить товары акции (sync — заменяет весь набор).
     * POST /admin/discount/promotion/{id}/products/sync
     */
    public function assignPromotionProducts(int $id, Request $request, UserPermission $userPermission): JsonResponse
    {
        $products = $this->normalizeProducts($request->input('products', []));

        $this->assignProductsToPromotionUseCase->execute($id, $products, $userPermission);

        return response()->json(['message' => 'Товары назначены'], Response::HTTP_OK);
    }

    /**
     * Добавить товары к акции (attach — дополняет существующие).
     * POST /admin/discount/promotion/{id}/products/attach
     */
    public function attachPromotionProducts(int $id, Request $request, UserPermission $userPermission)
    {
        if ($request->has('product_id')) {
            $products = [
                $request->integer('product_id') => (float)$request->input('price', 0),
            ];
        } else {
            $data = $request->input('products', []);
            if (count($data) === 0) {
                throw new \DomainException('Нет данных');
            }

            $products = $this->normalizeProducts($data);
        }

        $this->attachProductsToPromotionUseCase->execute($id, $products, $userPermission);

        return redirect()->back()->with('success', 'Товары добавлены');
    }

    /**
     * Отвязать товары от акции.
     * DELETE /admin/discount/promotion/{id}/products/detach
     */
    public function detachPromotionProducts(int $id, Request $request, UserPermission $userPermission)
    {
        if ($request->has('product_id')) {
            $productIds = [$request->integer('product_id')];
        } else {
            $productIds = $request->input('products', []);
        }

        $this->detachProductsFromPromotionUseCase->execute($id, $productIds, $userPermission);

        return redirect()->back();
        //return response()->json(['message' => 'Товары откреплены'], Response::HTTP_OK);
    }

    /**
     * Установить цену товара в акции.
     * POST /admin/discount/promotion/{id}/products/price
     */
    public function setPromotionProductPrice(int $id, Request $request, UserPermission $userPermission)
    {
        $dto = PromotionProductPriceData::validateAndCreate($request->all());

        $this->setProductPriceUseCase->execute($id, $dto, $userPermission);
        return redirect()->back();
        //return response()->json(['message' => 'Цена сохранена'], Response::HTTP_OK);
    }

    /**
     * Приводит входящие данные к формату [product_id => price].
     *
     * Поддерживаются форматы:
     *  - [product_id => price]  (ассоциативный массив)
     *  - [['product_id' => int, 'price' => float], ...]
     *
     * @param array $data
     * @return array<int, float>
     */
    private function normalizeProducts(array $data): array
    {
        $products = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $productId = (int)($value['product_id'] ?? 0);
                $price = (float)($value['price'] ?? 0);
            } else {
                $productId = (int)$key;
                $price = (float)$value;
            }

            if ($productId > 0) {
                $products[$productId] = $price;
            }
        }

        return $products;
    }
}
