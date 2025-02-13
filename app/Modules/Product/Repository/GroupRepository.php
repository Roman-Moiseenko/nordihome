<?php
declare(strict_types=1);

namespace App\Modules\Product\Repository;

use App\Modules\Discount\Entity\Promotion;
use App\Modules\Discount\Entity\PromotionGroup;
use App\Modules\Product\Entity\Group;
use App\Modules\Product\Entity\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GroupRepository
{
    public function getNotInPromotions(Promotion $promotion)
    {
        $promotions_id = Promotion::orderBy('id')
            ->where('finish_at', '>', Carbon::now())
            ->where(function ($query) use ($promotion) {
                $query->where('start_at', '<', $promotion->finish_at->format('Y-m-d'))
                    ->where('finish_at', '>', $promotion->start_at->format('Y-m-d'));
            })
            ->pluck('id')->toArray();

        $groups_id = PromotionGroup::whereIn('promotion_id', $promotions_id)->pluck('group_id')->toArray();

        return Group::orderBy('name')->whereNotIn('id', $groups_id)->get();
    }

    public function getIndex(Request $request, &$filters)
    {
        $query = Group::orderByDesc('id');
        $filters = [];
        if (!empty($name = $request['search'])) {
            $query = $query->where('name', 'LIKE', "%{$name}%");
        }


        if ($request->has('name')) {
            $name = $request->string('name')->value();
            $filters['name'] = $name;
            $query->whereRaw("LOWER(name) like LOWER('%$name%')");
        }
        if ($request->has('product')) {
            $product = $request->string('product')->value();
            $filters['product'] = $product;
            $query->whereHas('products', function ($query) use ($product) {
                $query->whereRaw("LOWER(name) like LOWER('%$product%')")
                    ->orWhere('code', 'like', "%$product%")
                    ->orWhere('code_search', 'like', "%$product%");
            });
        }
        if (count($filters) > 0) $filters['count'] = count($filters);
        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(Group $group) => $this->GroupToArray($group));
    }

    private function GroupToArray(Group $group): array
    {
        return array_merge($group->toArray(), [
            'quantity' => $group->products()->count(),
            'image' => $group->getImage(),
        ]);
    }

    public function GroupWithToArray(Group $group, Request $request): array
    {
        return array_merge($this->GroupToArray($group), [
            'products' => $group->products()->paginate($request->input('size', 20))
                ->withQueryString()->through(fn(Product $product) => [
                    'id' => $product->id,
                    'code' => $product->code,
                    'name' => $product->name,
                    'category' => $product->category->getParentNames(),
                ]),
        ]);
    }

    public function search(Group $group, Request $request)
    {
        $search = $request->string('search')->trim()->value();
        $group_ids = $group->products()->get()->pluck('id')->toArray();
        return Product::orderBy('name')
            ->where(function ($query) use ($search) {
                $query->where('code_search', 'LIKE', "%{$search}%")->orWhere('code', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%");
            })
            ->whereNotIn('id', $group_ids)
            ->where(function ($query) {
                $query->doesntHave('modification')->orHas('main_modification');
            })
            ->get()->map(fn(Product $product) => $product->toArrayForSearch());
    }


}
