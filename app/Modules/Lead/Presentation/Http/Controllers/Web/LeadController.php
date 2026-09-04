<?php

namespace App\Modules\Lead\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Application\Actions\Client\CreateClientUseCase;
use App\Modules\Auth\Application\DTOs\Client\ClientCreateData;
use App\Modules\Lead\Application\Actions\IndexByStatusLeadUseCase;
use App\Modules\Lead\Application\Services\StatusInWorkLeadService;
use App\Modules\Lead\Application\Services\StatusNotDecidedLeadService;
use App\Modules\Lead\Application\Services\StatusReturnNewLeadService;
use App\Modules\Lead\Domain\ValueObjects\LeadStatusValue;
use App\Modules\Lead\Infrastructure\Models\Lead;
use App\Modules\Lead\Infrastructure\Models\LeadStatus;
use App\Modules\Lead\Repository\LeadRepository;
use App\Modules\Lead\Service\LeadService;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use JetBrains\PhpStorm\Deprecated;

class LeadController extends Controller
{
    private LeadService $service;
    private LeadRepository $repository;

    public function __construct(
        LeadService $service,
        LeadRepository $repository,
        private CreateClientUseCase $createClientUseCase,
        private IndexByStatusLeadUseCase $indexByStatusLeadUseCase,
        private StatusInWorkLeadService $statusInWorkLeadService,
        private StatusNotDecidedLeadService $statusNotDecidedLeadService,
        private StatusReturnNewLeadService $statusReturnNewLeadService,
    )
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request): Response
    {
        $leads = $this->repository->getIndex($request);
        $boards = $this->repository->getBoards();
        return Inertia::render('Lead/Dashboard', [
            'leads' => $leads,
            'boards' => LeadStatusValue::STATUSES,

            //TODO Справочники, состояния и др.
        ]);
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


    #[Deprecated]
    public function set_status(Lead $lead, Request $request): RedirectResponse
    {
        $result = $this->service->setStatus($lead, $request);

        return redirect()->back()->with($result ? 'success' : 'error', $result ? 'Обновлено!' : 'Недопустимо!');
    }

    public function set_name(Lead $lead, Request $request): RedirectResponse
    {
        $this->service->setName($lead, $request);
        return redirect()->back()->with('success', 'Обновлено!');
    }

    public function add_item(Lead $lead, Request $request): RedirectResponse
    {
        $this->service->addItem($lead, $request);
        return redirect()->back()->with('success', 'Обновлено!');
    }

    public function set_comment(Lead $lead, Request $request): RedirectResponse
    {
        $this->service->setComment($lead, $request);
        return redirect()->back()->with('success', 'Обновлено!');
    }

    public function set_finished(Lead $lead, Request $request): RedirectResponse
    {
        $this->service->setFinished($lead, $request);
        return redirect()->back()->with('success', 'Обновлено!');
    }

    public function canceled(Lead $lead, Request $request): RedirectResponse
    {
        $this->service->canceled($lead, $request->integer('reason'));
        return redirect()->back()->with('success', 'Заявка отменена!');
    }

   /* public function completed(Lead $lead, Request $request): RedirectResponse
    {
        $this->service->completed($lead, $request);
        return redirect()->back()->with('success', 'Заявка завершена!');
    }*/

    public function create_user(Lead $lead, Request $request, UserPermission $permissions): RedirectResponse
    {
        //TODO Переделать передачу полей под DTO
        $dto = new ClientCreateData(
            lastName: $request->string('surname')->trim()->value(),
            firstName: $request->string('firstname')->trim()->value(),
            email: $request->string('email')->trim()->value(),
            middleName: $request->string('secondname')->trim()->value(),
            phone: $request->string('phone')->trim()->value(),
        );
        $client = $this->createClientUseCase->execute($dto, $permissions);

        $this->service->createUser($lead, $client);
        return redirect()->back()->with('success', 'Обновлено!');
    }

    public function create_order(Lead $lead, Request $request): RedirectResponse
    {
        $order = $this->service->createOrder($lead, $request);
        return redirect()->route('admin.order.show', $order)->with('success', 'Обновлено!');
    }

    public function getLeads(Request $request): Response
    {
        $staffId = auth()->user()->profileable_id;

        $leads = $this->indexByStatusLeadUseCase->execute($staffId, $request->input('status'));

        return \request()->json($leads);


    }

}
