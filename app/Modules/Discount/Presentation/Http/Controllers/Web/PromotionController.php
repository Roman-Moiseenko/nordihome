<?php
declare(strict_types=1);

namespace App\Modules\Discount\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Content\Application\Actions\ContentBlock\ListContentBlockByContainerUseCase;
use App\Modules\Content\Application\DTOs\ContentBlock\ContentBlockContainerData;
use App\Modules\Content\Domain\ValueObjects\ContainerType;
use App\Modules\Discount\Application\Actions\Promotion\CreatePromotionUseCase;
use App\Modules\Discount\Application\Actions\Promotion\IndexPromotionUseCase;
use App\Modules\Discount\Application\Actions\Promotion\StatusDraftPromotionUseCase;
use App\Modules\Discount\Application\Actions\Promotion\StatusFinishedPromotionUseCase;
use App\Modules\Discount\Application\Actions\Promotion\StatusStartedPromotionUseCase;
use App\Modules\Discount\Application\Actions\Promotion\StatusWaitingPromotionUseCase;
use App\Modules\Discount\Application\Actions\Promotion\UpdatePromotionUseCase;
use App\Modules\Discount\Application\Actions\Promotion\ViewPromotionUseCase;
use App\Modules\Discount\Application\DTOs\Promotion\PromotionCreateData;
use App\Modules\Discount\Application\DTOs\Promotion\PromotionIndexData;
use App\Modules\Discount\Application\DTOs\Promotion\PromotionUpdateData;
use App\Modules\Discount\Application\DTOs\Promotion\PromotionViewData;
use App\Modules\Discount\Domain\ValueObjects\PromotionStatus;
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
        private IndexPromotionUseCase $indexPromotionUseCase,
        private UpdatePromotionUseCase $updatePromotionUseCase,
        private StatusFinishedPromotionUseCase $finishedPromotionUseCase,
        private StatusStartedPromotionUseCase $startedPromotionUseCase,
        private StatusDraftPromotionUseCase $draftPromotionUseCase,
        private StatusWaitingPromotionUseCase $waitingPromotionUseCase,
        private ViewPromotionUseCase $viewPromotionUseCase,
        private ListContentBlockByContainerUseCase $listContentBlockByContainerUseCase,
    )
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request, UserPermission $permission): Response
    {
        $promotions = $this->indexPromotionUseCase->execute($permission);

        return Inertia::render('Discount/Promotion/Index', [
            'promotions' => PromotionIndexData::collect($promotions),
            'statuses' => array_select(PromotionStatus::STATUSES),
        ]);
    }

    public function store(Request $request, UserPermission $permission): RedirectResponse
    {
        $dto = PromotionCreateData::validateAndCreate($request->all());
        $promotion = $this->createPromotionUseCase->execute($dto, $permission);
        return redirect()->route('admin.discount.promotion.show', $promotion->id);
    }

    public function show(int $id, UserPermission $permission): Response
    {
        $promotion = $this->viewPromotionUseCase->execute($id, $permission);
        $dto = new ContentBlockContainerData($promotion->id, ContainerType::PROMOTION);
        $blocks = $this->listContentBlockByContainerUseCase->execute($dto);

        return Inertia::render('Discount/Promotion/Show', [
            'promotion' => PromotionViewData::fromEntity($promotion),
            'statuses' => array_select(PromotionStatus::STATUSES),
            'blocks' => $blocks,
        ]);
    }

    public function setInfo(int $id, Request $request, UserPermission $permission): RedirectResponse
    {
        $dto = PromotionUpdateData::validateAndCreate($request->all());
        $this->updatePromotionUseCase->execute($id, $dto, $permission);
        return redirect()->back()->with('success', 'Сохранено');
    }

/*

    //MAINDO useCase
    public function add_product(Request $request, Promotion $promotion, UserPermission $permission): RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|integer|gt:0',
        ]);
        $this->service->addProduct($promotion, (int)$request['product_id']);
        return redirect()->back()->with('success', 'Товар добавлен');
    }

    //MAINDO useCase
    public function add_products(Request $request, Promotion $promotion, UserPermission $permission): RedirectResponse
    {
        $this->service->addProducts($promotion, $request['products']);
        return redirect()->back()->with('success', 'Товары добавлены');
    }

    //MAINDO useCase
    public function del_product(Promotion $promotion, Request $request, UserPermission $permission): RedirectResponse
    {
        $this->service->delProduct($request, $promotion);
        return redirect()->back()->with('success', 'Товар удален');
    }
*/
    //MAINDO useCase
    public function set_product(Request $request, Promotion $promotion, UserPermission $permission): RedirectResponse
    {
        $this->service->setProduct($request, $promotion);
        return redirect()->back()->with('success', 'Сохранено');
    }

    //MAINDO useCase
    public function destroy(Promotion $promotion, UserPermission $permission): RedirectResponse
    {
        $this->service->delete($promotion);
        return redirect()->back()->with('success', 'Акция удалена');
    }

    public function draft(int $id, UserPermission $permission): RedirectResponse
    {
        $this->draftPromotionUseCase->execute($id, $permission);
        return redirect()->back()->with('success', 'Акция отключена');
    }

    public function waiting(int $id, UserPermission $permission): RedirectResponse
    {
        $this->waitingPromotionUseCase->execute($id, $permission);
        return redirect()->back()->with('success', 'Акция добавлена в очередь');
    }

    public function stop(int $id, UserPermission $permission): RedirectResponse
    {
        $this->finishedPromotionUseCase->execute($id, $permission);
        return redirect()->back()->with('success', 'Акция остановлена в ручную');
    }

    public function start(int $id, UserPermission $permission): RedirectResponse
    {
        $this->startedPromotionUseCase->execute($id, $permission);
        return redirect()->back()->with('success', 'Акция запущена в ручную');
    }

}
