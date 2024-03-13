<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Discount;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Discount\Entity\Promotion;
use App\Modules\Discount\Repository\PromotionRepository;
use App\Modules\Discount\Service\PromotionService;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\GroupRepository;
use App\Modules\Product\Repository\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class PromotionController extends Controller
{
    private PromotionService $service;
    private PromotionRepository $repository;
    private mixed $pagination;
    private GroupRepository $groups;
    private ProductRepository $products;

    public function __construct(PromotionService $service, PromotionRepository $repository, GroupRepository $groups, ProductRepository $products)
    {
        $this->service = $service;
        $this->repository = $repository;
        $this->pagination = Config::get('shop-config.p-list');
        $this->groups = $groups;
        $this->products = $products;
    }

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use ($request) {
            $pagination = $request['p'] ?? $this->pagination;
            $promotions = $this->repository->getIndex($pagination);
            if (isset($request['p'])) {
                $promotions->appends(['p' => $pagination]);
            }
            return view('admin.discount.promotion.index', compact('promotions', 'pagination'));
        });
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
        return $this->try_catch_admin(function () use ($request) {
            $promotion = $this->service->create($request);
            return redirect()->route('admin.discount.promotion.show', compact('promotion'));
        });
    }

    public function show(Promotion $promotion)
    {
        return $this->try_catch_admin(function () use ($promotion) {
            $groups = $this->groups->getNotInPromotions($promotion);
            return view('admin.discount.promotion.show', compact('promotion', 'groups'));
        });
    }

    public function edit(Promotion $promotion)
    {
        return view('admin.discount.promotion.edit', compact('promotion'));
    }

    public function update(Request $request, Promotion $promotion)
    {
        return $this->try_catch_admin(function () use ($request, $promotion) {
            $promotion = $this->service->update($request, $promotion);
            return redirect()->route('admin.discount.promotion.show', compact('promotion'));
        });
    }

    public function add_product(Request $request, Promotion $promotion)
    {
        $request->validate([
            'product_id' => 'required|integer|gt:0',
        ]);
        return $this->try_catch_admin(function () use ($request, $promotion) {
            $this->service->add_product($request, $promotion);
            return redirect()->route('admin.discount.promotion.show', compact('promotion'));
        });
    }

    public function del_product(Promotion $promotion, Product $product)
    {
        return $this->try_catch_admin(function () use ($promotion, $product) {
            $this->service->del_product($product, $promotion);
            return redirect()->route('admin.discount.promotion.show', compact('promotion'));
        });
    }

    public function set_product(Request $request, Promotion $promotion, Product $product)
    {
        return $this->try_catch_ajax_admin(function () use ($request, $promotion, $product) {
            $this->service->set_product($request, $promotion, $product);
            return response()->json(true);
        });
    }

    /*
        public function add_group(Request $request, Promotion $promotion)
        {
            $request->validate([
                'group_id' => 'required|integer|gt:0',
                'discount' => 'required',
            ]);

            try {
                $this->service->add_group($request, $promotion);
                return redirect()->route('admin.discount.promotion.show', compact('promotion'));
            } catch (\DomainException $e) {
                flash($e->getMessage(), 'danger');
            } catch (\Throwable $e) {
                event(new ThrowableHasAppeared($e));
                flash('Техническая ошибка! Информация направлена разработчику', 'danger');
            }
            return redirect()->back();

        }

        public function del_group(Promotion $promotion, Group $group)
        {
            try {
                $this->service->del_group($group, $promotion);
                return redirect()->route('admin.discount.promotion.show', compact('promotion'));
            } catch (\DomainException $e) {
                flash($e->getMessage(), 'danger');
            } catch (\Throwable $e) {
                event(new ThrowableHasAppeared($e));
                flash('Техническая ошибка! Информация направлена разработчику', 'danger');
            }
            return redirect()->back();
        }

        public function set_group(Request $request, Promotion $promotion)
        {
            try {
                $this->service->set_group($request, $promotion);
                return redirect()->route('admin.discount.promotion.show', compact('promotion'));
            } catch (\DomainException $e) {
                flash($e->getMessage(), 'danger');
            } catch (\Throwable $e) {
                event(new ThrowableHasAppeared($e));
                flash('Техническая ошибка! Информация направлена разработчику', 'danger');
            }
            return redirect()->back();
        }

    */
    public function destroy(Promotion $promotion)
    {
        return $this->try_catch_admin(function () use ($promotion) {
            $this->service->delete($promotion);
            return redirect()->route('admin.discount.promotion.index');
        });
    }

    //Команды
    public function draft(Promotion $promotion)
    {
        return $this->try_catch_admin(function () use ($promotion) {
            $this->service->draft($promotion);
            return redirect()->back();
        });
    }

    public function published(Promotion $promotion)
    {
        return $this->try_catch_admin(function () use ($promotion) {
            $this->service->published($promotion);
            return redirect()->back();
        });
    }

    public function stop(Promotion $promotion)
    {
        return $this->try_catch_admin(function () use ($promotion) {
            $this->service->stop($promotion);
            return redirect()->back();
        });
    }

    public function start(Promotion $promotion)
    {
        return $this->try_catch_admin(function () use ($promotion) {
            $this->service->start($promotion);
            return redirect()->back();
        });
    }

    public function search(Request $request, Promotion $promotion)
    {
        return $this->try_catch_ajax_admin(function () use ($request, $promotion) {
            $result = [];
            $products = $this->products->search($request['search']);
            /** @var Product $product */
            foreach ($products as $product) {
                if (!$promotion->isProduct($product->id)) {
                    $result[] = $this->products->toArrayForSearch($product);
                }
            }
            return \response()->json($result);
        });
    }
}
