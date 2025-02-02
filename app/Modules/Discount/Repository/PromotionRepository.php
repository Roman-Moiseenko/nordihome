<?php
declare(strict_types=1);

namespace App\Modules\Discount\Repository;

use App\Modules\Discount\Entity\Promotion;
use App\Modules\Product\Entity\Product;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class PromotionRepository
{

    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = Promotion::orderBy('finish_at', 'DESC')->orderBy('start_at');
        $filters = [];
        if ($request->has('product')) {
            $product = $request->string('product');
            $filters['product'] = $product;
            $query->whereHas('products', function ($query) use ($product) {
                $query->where(function ($q) use ($product) {
                    $q->whereRaw("LOWER(name) like LOWER('%$product%')")
                        ->orWhere('code', 'like', "%$product%")
                        ->orWhere('code_search', 'like', "%$product%");
                });
            });
        }

        if (($status = $request->integer('status')) > 0) {
            $filters['status'] = $status;
            if ($status == Promotion::STATUS_DRAFT) $query->where('published', false);
            if ($status == Promotion::STATUS_WAITING)
                $query->where('published', true)->where('active', false)->where(function ($query) {
                    $query->where('start_at', null)->orWhere('start_at', '>', now());
                });
            if ($status == Promotion::STATUS_STARTED)
                $query->where('published', true)->where('active', true);
            if ($status == Promotion::STATUS_FINISHED)
                $query->where('published', true)->where('active', false)->where('finish_at', '<', now());
        }

        if (count($filters) > 0) $filters['count'] = count($filters);
        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(Promotion $promotion) => $this->PromotionToArray($promotion));
    }

    private function PromotionToArray(Promotion $promotion): array
    {
        return array_merge($promotion->toArray(), [
            'status' => $promotion->status(),
            'image' => $promotion->getImage(),
            'is_finished' => $promotion->isFinished(),
            'quantity' => $promotion->products()->count(),
        ]);
    }

    public function PromotionWithToArray(Promotion $promotion): array
    {
        return array_merge($this->PromotionToArray($promotion), [
            'icon' => $promotion->getIcon(),
            'products' => $promotion->products()->get()->map(function (Product $product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'code' => $product->code,
                    'image' => $product->miniImage(),
                    'price' => $product->getPriceRetail(),
                    'discount' => $product->pivot->price
                ];
            })
        ]);
    }

    public function getActive()
    {
        $promotions = [];
        /** @var Promotion $promotion */
        foreach (Promotion::where('published', true)->get() as $promotion) {
             if ($promotion->status() == Promotion::STATUS_STARTED)
             $promotions[] = $promotion;
         }
        return $promotions;
    }
}
