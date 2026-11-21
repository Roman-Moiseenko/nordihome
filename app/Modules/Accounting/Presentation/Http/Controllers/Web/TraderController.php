<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Application\Actions\Trader\AttachOrganizationToTraderUseCase;
use App\Modules\Accounting\Application\Actions\Trader\CreateTraderUseCase;
use App\Modules\Accounting\Application\Actions\Trader\DetachOrganizationFromTraderUseCase;
use App\Modules\Accounting\Application\Actions\Trader\IndexTraderQuery;
use App\Modules\Accounting\Application\Actions\Trader\ListTradersUseCase;
use App\Modules\Accounting\Application\Actions\Trader\UpdateTraderUseCase;
use App\Modules\Accounting\Application\Actions\Trader\ViewTraderQuery;
use App\Modules\Accounting\Application\DTOs\Trader\TraderCreateData;
use App\Modules\Accounting\Application\DTOs\Trader\TraderOrganizationSetData;
use App\Modules\Accounting\Application\DTOs\Trader\TraderUpdateData;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TraderController extends Controller
{
    public function __construct(
        private readonly IndexTraderQuery $indexTraderQuery,
        private readonly ViewTraderQuery $viewTraderQuery,
        private readonly CreateTraderUseCase $createTraderUseCase,
        private readonly UpdateTraderUseCase $updateTraderUseCase,
        private readonly AttachOrganizationToTraderUseCase $attachOrganizationToTraderUseCase,
        private readonly DetachOrganizationFromTraderUseCase $detachOrganizationFromTraderUseCase,
        private readonly ListTradersUseCase $listTradersUseCase,
    ) {
    }

    public function index(Request $request, UserPermission $permission): Response
    {
        $traders = $this->indexTraderQuery->execute($permission);

        return Inertia::render('Accounting/Trader/Index', [
            'traders' => $traders,
        ]);
    }

    public function store(Request $request, UserPermission $permission): RedirectResponse
    {
        $dto = TraderCreateData::validateAndCreate($request->all());
        $trader = $this->createTraderUseCase->execute($dto, $permission);

        return redirect()->route('admin.accounting.trader.show', $trader->id)
            ->with('success', 'Продавец добавлен');
    }

    public function show(int $id, UserPermission $permission): Response
    {
        return Inertia::render('Accounting/Trader/Show', [
            'trader' => $this->viewTraderQuery->execute($id, $permission),
        ]);
    }

    public function update(int $id, Request $request, UserPermission $permission): RedirectResponse
    {
        $dto = TraderUpdateData::validateAndCreate($request->all());
        $trader = $this->updateTraderUseCase->execute($id, $dto, $permission);

        return redirect()->route('admin.accounting.trader.show', $trader->id)
            ->with('success', 'Сохранено');
    }

    public function destroy(int $id, UserPermission $permission): RedirectResponse
    {
        throw new \DomainException('Удалять продавца программно нельзя!');
    }

    public function attach(int $id, Request $request, UserPermission $permission): RedirectResponse
    {
        $dto = TraderOrganizationSetData::validateAndCreate($request->all());
        $this->attachOrganizationToTraderUseCase->execute($id, $dto, $permission);

        return redirect()->back()->with('success', 'Организация добавлена к продавцу');
    }

    public function detach(int $id, Request $request, UserPermission $permission): RedirectResponse
    {
        $dto = TraderOrganizationSetData::validateAndCreate($request->all());
        $this->detachOrganizationFromTraderUseCase->execute($id, $dto, $permission);

        return redirect()->back()->with('success', 'Организация отсоединена от продавца');
    }

    public function list(): JsonResponse
    {
        return response()->json($this->listTradersUseCase->execute());
    }
}
