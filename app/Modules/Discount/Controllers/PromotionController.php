<?php
declare(strict_types=1);

namespace App\Modules\Discount\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Discount\Entity\Promotion;
use App\Modules\Discount\Repository\PromotionRepository;
use App\Modules\Discount\Service\PromotionService;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PromotionController extends Controller
{
    private PromotionService $service;
    private PromotionRepository $repository;

    public function __construct(
        PromotionService    $service,
        PromotionRepository $repository,
    )
    {
        $this->middleware(['auth:admin', 'can:discount']);
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request): \Inertia\Response
    {
        $promotions = $this->repository->getIndex($request, $filters);
        return Inertia::render('Discount/Promotion/Index', [
            'promotions' => $promotions,
            'filters' => $filters,
            'statuses' => array_select(Promotion::STATUSES),
        ]);
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
        return Inertia::render('Discount/Promotion/Show', [
            'promotion' => $this->repository->PromotionWithToArray($promotion),
            'statuses' => array_select(Promotion::STATUSES),
        ]);
    }

    public function set_info(Request $request, Promotion $promotion): RedirectResponse
    {
        try {
            $this->service->setInfo($request, $promotion);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function add_product(Request $request, Promotion $promotion): RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|integer|gt:0',
        ]);
        try {
            $this->service->addProduct($promotion, (int)$request['product_id']);
            return redirect()->back()->with('success', 'Товар добавлен');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

    }

    public function add_products(Request $request, Promotion $promotion): RedirectResponse
    {
        try {
            $this->service->addProducts($promotion, $request['products']);
            return redirect()->back()->with('success', 'Товары добавлены');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function del_product(Promotion $promotion, Request $request): RedirectResponse
    {
        try {
            $this->service->delProduct($request, $promotion);
            return redirect()->back()->with('success', 'Товар удален');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function set_product(Request $request, Promotion $promotion): RedirectResponse
    {
        try {
            $this->service->setProduct($request, $promotion);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Promotion $promotion): RedirectResponse
    {
        try {
            $this->service->delete($promotion);
            return redirect()->back()->with('success', 'Акция удалена');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    //Команды
    public function toggle(Promotion $promotion)
    {
        try {
            if ($promotion->isPublished()) {
                $this->service->draft($promotion);
                $success = 'Акция отключена';
            } else {
                $this->service->published($promotion);
                $success = 'Акция добавлена в очередь';
            }
            return redirect()->back()->with('success', $success);
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function stop(Promotion $promotion): RedirectResponse
    {
        try {
            $this->service->stop($promotion);
            return redirect()->back()->with('success', 'Акция остановлена в ручную');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function start(Promotion $promotion): RedirectResponse
    {
        try {
            $this->service->start($promotion);
            return redirect()->back()->with('success', 'Акция запущена в ручную');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

}
