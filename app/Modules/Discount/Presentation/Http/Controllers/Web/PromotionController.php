<?php
declare(strict_types=1);

namespace App\Modules\Discount\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Content\Application\Actions\ContentBlock\ListContentBlockByContainerUseCase;
use App\Modules\Content\Application\DTOs\ContentBlock\ContentBlockContainerData;
use App\Modules\Content\Domain\ValueObjects\ContainerType;
use App\Modules\Discount\Application\Actions\Promotion\CreatePromotionUseCase;
use App\Modules\Discount\Application\Actions\Promotion\IndexPromotionUseCase;
use App\Modules\Discount\Application\Actions\Promotion\RemovePromotionUseCase;
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
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PromotionController extends Controller
{

    public function __construct(
        private readonly CreatePromotionUseCase             $createPromotionUseCase,
        private readonly IndexPromotionUseCase              $indexPromotionUseCase,
        private readonly UpdatePromotionUseCase             $updatePromotionUseCase,
        private readonly StatusFinishedPromotionUseCase     $finishedPromotionUseCase,
        private readonly StatusStartedPromotionUseCase      $startedPromotionUseCase,
        private readonly StatusDraftPromotionUseCase        $draftPromotionUseCase,
        private readonly StatusWaitingPromotionUseCase      $waitingPromotionUseCase,
        private readonly ViewPromotionUseCase               $viewPromotionUseCase,
        private readonly ListContentBlockByContainerUseCase $listContentBlockByContainerUseCase,
        private readonly RemovePromotionUseCase $removePromotionUseCase,
    )
    {
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

    public function destroy(int $id, Promotion $promotion, UserPermission $permission): RedirectResponse
    {
        $this->removePromotionUseCase->execute($id, $permission);
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

    public function list(): JsonResponse
    {
        //MAINDO Список дейстующих акций
        $list = [];
        return response()->json($list);
    }

}
