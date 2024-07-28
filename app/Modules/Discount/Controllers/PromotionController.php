<?php
declare(strict_types=1);

namespace App\Modules\Discount\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Discount\Entity\Promotion;
use App\Modules\Discount\Repository\PromotionRepository;
use App\Modules\Discount\Service\PromotionService;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\GroupRepository;
use App\Modules\Product\Repository\ProductRepository;
use App\UseCase\PaginationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use JetBrains\PhpStorm\Deprecated;

class PromotionController extends Controller
{
    private PromotionService $service;
    private PromotionRepository $repository;
    private GroupRepository $groups;
    private ProductRepository $products;

    public function __construct(
        PromotionService    $service,
        PromotionRepository $repository,
        GroupRepository     $groups,
        ProductRepository   $products)
    {
        $this->middleware(['auth:admin', 'can:discount']);
        $this->service = $service;
        $this->repository = $repository;
        $this->groups = $groups;
        $this->products = $products;
    }

    public function index(Request $request)
    {
        $query = $this->repository->getIndex();
        $promotions = $this->pagination($query, $request, $pagination);
        return view('admin.discount.promotion.index', compact('promotions', 'pagination'));
    }

    public function create()
    {
        return view('admin.discount.promotion.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ]);
        $promotion = $this->service->create($request);
        return redirect()->route('admin.discount.promotion.show', compact('promotion'));
    }

    public function show(Promotion $promotion)
    {
        $groups = $this->groups->getNotInPromotions($promotion);
        return view('admin.discount.promotion.show', compact('promotion', 'groups'));
    }

    public function edit(Promotion $promotion)
    {
        return view('admin.discount.promotion.edit', compact('promotion'));
    }

    public function update(Request $request, Promotion $promotion)
    {
        $this->service->update($request, $promotion);
        return redirect()->route('admin.discount.promotion.show', compact('promotion'));
    }

    public function add_product(Request $request, Promotion $promotion)
    {
        $request->validate([
            'product_id' => 'required|integer|gt:0',
        ]);
        $this->service->add_product($promotion, (int)$request['product_id']);
        return redirect()->route('admin.discount.promotion.show', compact('promotion'));
    }

    public function add_products(Request $request, Promotion $promotion)
    {
        $this->service->add_products($promotion, $request['products']);
        return redirect()->route('admin.discount.promotion.show', compact('promotion'));
    }

    public function del_product(Promotion $promotion, Product $product)
    {
        $this->service->del_product($product, $promotion);
        return redirect()->route('admin.discount.promotion.show', compact('promotion'));
    }

    public function set_product(Request $request, Promotion $promotion, Product $product)
    {
        $this->service->set_product($request, $promotion, $product);
        return response()->json(true);
    }

    public function destroy(Promotion $promotion)
    {
        $this->service->delete($promotion);
        return redirect()->route('admin.discount.promotion.index');
    }

    //Команды
    public function draft(Promotion $promotion)
    {
        $this->service->draft($promotion);
        return redirect()->back();
    }

    public function published(Promotion $promotion)
    {
        $this->service->published($promotion);
        return redirect()->back();
    }

    public function stop(Promotion $promotion)
    {
        $this->service->stop($promotion);
        return redirect()->back();
    }

    public function start(Promotion $promotion)
    {
        $this->service->start($promotion);
        return redirect()->back();
    }

    #[Deprecated]
    public function search(Request $request, Promotion $promotion)
    {
        $result = [];
        $products = $this->products->search($request['search']);
        /** @var Product $product */
        foreach ($products as $product) {
            if (!$promotion->isProduct($product->id)) {
                $result[] = $this->products->toArrayForSearch($product);
            }
        }
        return \response()->json($result);
    }
}
