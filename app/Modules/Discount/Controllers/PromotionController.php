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
use Inertia\Response;

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

    public function index(Request $request): Response
    {
        $promotions = $this->repository->getIndex($request, $filters);
        return Inertia::render('Discount/Promotion/Index', [
            'promotions' => $promotions,
            'filters' => $filters,
            'statuses' => array_select(Promotion::STATUSES),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string'
        ]);
        $promotion = $this->service->create($request);
        return redirect()->route('admin.discount.promotion.show', compact('promotion'));
    }

    public function show(Promotion $promotion): Response
    {
        return Inertia::render('Discount/Promotion/Show', [
            'promotion' => $this->repository->PromotionWithToArray($promotion),
            'statuses' => array_select(Promotion::STATUSES),
        ]);
    }

    public function set_info(Request $request, Promotion $promotion): RedirectResponse
    {
        $this->service->setInfo($request, $promotion);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function add_product(Request $request, Promotion $promotion): RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|integer|gt:0',
        ]);
        $this->service->addProduct($promotion, (int)$request['product_id']);
        return redirect()->back()->with('success', 'Товар добавлен');
    }

    public function add_products(Request $request, Promotion $promotion): RedirectResponse
    {
        $this->service->addProducts($promotion, $request['products']);
        return redirect()->back()->with('success', 'Товары добавлены');
    }

    public function del_product(Promotion $promotion, Request $request): RedirectResponse
    {
        $this->service->delProduct($request, $promotion);
        return redirect()->back()->with('success', 'Товар удален');
    }

    public function set_product(Request $request, Promotion $promotion): RedirectResponse
    {
        $this->service->setProduct($request, $promotion);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(Promotion $promotion): RedirectResponse
    {
        $this->service->delete($promotion);
        return redirect()->back()->with('success', 'Акция удалена');
    }

    //Команды
    public function toggle(Promotion $promotion): RedirectResponse
    {
        if ($promotion->isPublished()) {
            $this->service->draft($promotion);
            $success = 'Акция отключена';
        } else {
            $this->service->published($promotion);
            $success = 'Акция добавлена в очередь';
        }
        return redirect()->back()->with('success', $success);
    }

    public function stop(Promotion $promotion): RedirectResponse
    {
        $this->service->stop($promotion);
        return redirect()->back()->with('success', 'Акция остановлена в ручную');
    }

    public function start(Promotion $promotion): RedirectResponse
    {
        $this->service->start($promotion);
        return redirect()->back()->with('success', 'Акция запущена в ручную');

    }

}
