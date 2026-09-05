<?php

namespace App\Modules\Lead\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Application\DTOs\Client\ClientCreateData;
use App\Modules\Lead\Application\Actions\AddCommentLeadUseCase;
use App\Modules\Lead\Application\Actions\IndexByStatusLeadUseCase;
use App\Modules\Lead\Application\Actions\SetNameLeadUseCase;
use App\Modules\Lead\Application\DTOs\Lead\LeadItemAddData;
use App\Modules\Lead\Application\Services\CreatAndAssignClientLeadService;
use App\Modules\Lead\Application\Services\CreateOrderFromLeadService;
use App\Modules\Lead\Application\Services\StatusInWorkLeadService;
use App\Modules\Lead\Application\Services\StatusNotDecidedLeadService;
use App\Modules\Lead\Application\Services\StatusReturnNewLeadService;
use App\Modules\Lead\Infrastructure\Models\Lead;
use App\Modules\Lead\Service\LeadService;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LeadController extends Controller
{
    private LeadService $service;

    public function __construct(
        LeadService $service,
        private IndexByStatusLeadUseCase $indexByStatusLeadUseCase,
        private StatusInWorkLeadService $statusInWorkLeadService,
        private StatusNotDecidedLeadService $statusNotDecidedLeadService,
        private StatusReturnNewLeadService $statusReturnNewLeadService,
        private AddCommentLeadUseCase $addCommentLeadUseCase,
        private CreatAndAssignClientLeadService $creatAndAssignClientLeadService,
        private SetNameLeadUseCase $setNameLeadUseCase,
        private CreateOrderFromLeadService $createOrderFromLeadService,
    )
    {
        $this->service = $service;
    }

    public function index(Request $request): Response
    {
        // Каждая панель загружает свои данные отдельно через маршрут admin.lead.leads
        return Inertia::render('Lead/Dashboard');
    }

    public function setInWork(int $id, UserPermission $permission)
    {
        $staffId = auth()->user()->profileable_id;
        $this->statusInWorkLeadService->execute($id, $staffId, $permission);
        return redirect()->back()->with( 'Обновлено!');
    }

    public function setNotDecided(int $id, UserPermission $permission)
    {
        $staffId = auth()->user()->profileable_id;
        $this->statusNotDecidedLeadService->execute($id, $staffId, $permission);
        return redirect()->back()->with( 'Обновлено!');
    }

    public function setReturnNew(int $id, UserPermission $permission)
    {
        $staffId = auth()->user()->profileable_id;
        $this->statusReturnNewLeadService->execute($id, $staffId, $permission);
        return redirect()->back()->with( 'Обновлено!');
    }

    public function setName(int $id, Request $request): RedirectResponse
    {
        $this->setNameLeadUseCase->execute($id, $request->input('name'));

        return redirect()->back()->with('success', 'Обновлено!');
    }

    public function createOrder(int $id, Request $request, UserPermission $permission): RedirectResponse
    {
        $order = $this->createOrderFromLeadService->execute($id, $permission);
        return redirect()->route('admin.order.show', $order)->with('success', 'Обновлено!');
    }

    public function addComment(int $id, Request $request, UserPermission $permission): RedirectResponse
    {
        $dto = LeadItemAddData::validateAndCreate($request->all());
        $dto->staffId = auth()->user()->profileable_id;
        $this->addCommentLeadUseCase->execute($id, $dto, $permission);

        return redirect()->back()->with('success', 'Обновлено!');
    }

    public function canceled(Lead $lead, Request $request): RedirectResponse
    {
        $this->service->canceled($lead, $request->integer('reason'));
        return redirect()->back()->with('success', 'Заявка отменена!');
    }

    public function createClient(int $id, Request $request, UserPermission $permissions): RedirectResponse
    {
        $dto = ClientCreateData::validateAndCreate($request->all());
        $this->creatAndAssignClientLeadService->execute($id, $dto, $permissions);

        return redirect()->back()->with('success', 'Обновлено!');
    }


    /**
     * API запрос для каждой панели
     */
    public function getLeads(Request $request)
    {
        $staffId = auth()->user()->profileable_id;
        $leads = $this->indexByStatusLeadUseCase->execute($staffId, $request->input('status'));

        return response()->json($leads);
    }

}
