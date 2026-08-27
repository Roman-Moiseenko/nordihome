<?php
declare(strict_types=1);

namespace App\Modules\Discount\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Discount\Application\Actions\Promotion\CreatePromotionUseCase;
use App\Modules\Discount\Application\Actions\Promotion\UpdatePromotionUseCase;
use App\Modules\Discount\Application\DTOs\Promotion\PromotionCreateData;
use App\Modules\Discount\Application\DTOs\Promotion\PromotionUpdateData;
use App\Modules\Discount\Infrastructure\Models\Promotion;
use App\Modules\Discount\Repository\PromotionRepository;
use App\Modules\Discount\Service\PromotionService;
use App\Modules\Shared\Domain\Entities\UserPermission;
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
        private CreatePromotionUseCase $createPromotionUseCase,
        private UpdatePromotionUseCase $updatePromotionUseCase,
    )
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request, UserPermission $permission): Response
    {
        $promotions = $this->repository->getIndex($request, $filters);
        return Inertia::render('Discount/Promotion/Index', [
            'promotions' => $promotions,
            'filters' => $filters,
            'statuses' => array_select(Promotion::STATUSES),
        ]);
    }

    public function store(Request $request, UserPermission $permission): RedirectResponse
    {
        $dto = PromotionCreateData::validateAndCreate($request->all());

        $promotion = $this->createPromotionUseCase->execute($dto, $permission);

       // $promotion = $this->service->create($request);
        return redirect()->route('admin.discount.promotion.show', $promotion->id);
    }

    public function show(Promotion $promotion, UserPermission $permission): Response
    {
        return Inertia::render('Discount/Promotion/Show', [
            'promotion' => $this->repository->PromotionWithToArray($promotion),
            'statuses' => array_select(Promotion::STATUSES),
        ]);
    }

    public function set_info(int $id, Request $request, UserPermission $permission): RedirectResponse
    {
        $dto = PromotionUpdateData::validateAndCreate($request->all());
        $this->updatePromotionUseCase->execute($id, $dto, $permission);
        //$this->service->setInfo($request, $promotion);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function add_product(Request $request, Promotion $promotion, UserPermission $permission): RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|integer|gt:0',
        ]);
        $this->service->addProduct($promotion, (int)$request['product_id']);
        return redirect()->back()->with('success', 'Товар добавлен');
    }

    public function add_products(Request $request, Promotion $promotion, UserPermission $permission): RedirectResponse
    {
        $this->service->addProducts($promotion, $request['products']);
        return redirect()->back()->with('success', 'Товары добавлены');
    }

    public function del_product(Promotion $promotion, Request $request, UserPermission $permission): RedirectResponse
    {
        $this->service->delProduct($request, $promotion);
        return redirect()->back()->with('success', 'Товар удален');
    }

    public function set_product(Request $request, Promotion $promotion, UserPermission $permission): RedirectResponse
    {
        $this->service->setProduct($request, $promotion);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(Promotion $promotion, UserPermission $permission): RedirectResponse
    {
        $this->service->delete($promotion);
        return redirect()->back()->with('success', 'Акция удалена');
    }

    //Команды
    public function toggle(Promotion $promotion, UserPermission $permission): RedirectResponse
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

    public function stop(Promotion $promotion, UserPermission $permission): RedirectResponse
    {
        $this->service->stop($promotion);
        return redirect()->back()->with('success', 'Акция остановлена в ручную');
    }

    public function start(Promotion $promotion, UserPermission $permission): RedirectResponse
    {
        $this->service->start($promotion);
        return redirect()->back()->with('success', 'Акция запущена в ручную');

    }

}
