<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Repository;

use App\Modules\Accounting\Application\Actions\ProductPrice\GetLatestPricesQuery;
use App\Modules\Accounting\Application\Actions\ProductPrice\GetPreviousPricesQuery;
use App\Modules\Accounting\Domain\ValueObjects\PriceType;
use App\Modules\Accounting\Entity\PricingDocument;
use App\Modules\Accounting\Entity\PricingProduct;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class PricingRepository extends AccountingRepository
{

    public function __construct(
        private readonly GetLatestPricesQuery   $latestPricesQuery,
        private readonly GetPreviousPricesQuery $previousPricesQuery,
    )
    {
    }

    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = PricingDocument::withTrashed()->orderByDesc('created_at');

        $this->filters($query, $filters, $request, function (&$query, &$filters, $request) {
            if ($request->integer('distributor') > 0) {
                $distributor = $request->integer('distributor');
                $filters['distributor'] = $distributor;
                $query->whereHas('arrival', function ($query) use ($distributor) {
                    $query->whereHas('distributor', function ($query) use ($distributor) {
                        $query->where('id', $distributor);
                    });
                });
            }
        }, false);

        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(PricingDocument $document) => $this->PricingToArray($document));
    }

    public function PricingToArray(PricingDocument $document): array
    {
        return array_merge($document->toArray(), [
            'trashed' => $document->trashed(),
            'staff' => !is_null($document->staff) ? $document->staff->fullName : '-',
            'arrival' => is_null($document->arrival_id) ? null : $document->arrival()->first()->toArray(),
        ]);
    }

    public function PricingWithToArray(PricingDocument $document, Request $request, &$filters): array
    {
        $query = $this->productFilters($document, $request, $filters);
        return array_merge(
            $this->commonItems($document),
            $this->PricingToArray($document),
            [
                'products' => $query
                    ->paginate($request->input('size', 20))
                    ->withQueryString()
                    ->through(function (PricingProduct $product) use ($document) {

                        $prices = $this->prices($product->id, $document->isCompleted());
                        return array_merge($product->toArray(), [
                            'name' => $product->product->name,
                            'code' => $product->product->code,
                            'price_cost_old' => $prices[PriceType::COST] ?? 0,
                            'price_retail_old' => $prices[PriceType::RETAIL] ?? 0,
                            'price_bulk_old' => $prices[PriceType::BULK] ?? 0,
                            'price_special_old' => $prices[PriceType::SPECIAL] ?? 0,
                            'price_min_old' => $prices[PriceType::MINIMAL] ?? 0,
                            'price_pre_old' => $prices[PriceType::PREORDER] ?? 0,
                        ]);
                    }
                    ),
            ]);
    }

    private function prices(int $productId, bool $previous)
    {
        if ($previous) return $this->previousPricesQuery->execute($productId);
        return $this->latestPricesQuery->execute($productId);
    }
}
