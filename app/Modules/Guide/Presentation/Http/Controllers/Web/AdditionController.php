<?php
declare(strict_types=1);

namespace App\Modules\Guide\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Guide\Application\Actions\Addition\CreateAdditionUseCase;
use App\Modules\Guide\Application\Actions\Addition\IndexAdditionUseCase;
use App\Modules\Guide\Application\Actions\Addition\ListGroupAdditionUseCase;
use App\Modules\Guide\Application\Actions\Addition\RemoveAdditionUseCase;
use App\Modules\Guide\Application\Actions\Addition\UpdateAdditionUseCase;
use App\Modules\Guide\Application\DTOs\Addition\AdditionCreateData;
use App\Modules\Guide\Application\DTOs\Addition\AdditionUpdateData;
use App\Modules\Guide\Domain\ValueObjects\AdditionType;
use App\Modules\Guide\Infrastructure\Models\Addition;
use App\Modules\Order\Entity\Addition\CalculateAddition;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class AdditionController extends Controller
{

    public function __construct(
        private readonly IndexAdditionUseCase $indexAdditionUseCase,
        private readonly CreateAdditionUseCase $createAdditionUseCase,
        private readonly UpdateAdditionUseCase $updateAdditionUseCase,
        private readonly RemoveAdditionUseCase $removeAdditionUseCase,
        private readonly ListGroupAdditionUseCase $listGroupAdditionUseCase,
    )
    {
    }

    public function index(UserPermission $permission): Response
    {

        $types = array_select(AdditionType::TYPES);
        $classes = array_select(CalculateAddition::CLASSES);
        $additions = $this->indexAdditionUseCase->execute($permission);

        return Inertia::render('Guide/Addition', [
            'additions' => $additions,
            'types' => $types,
            'classes' => $classes,
        ]);
    }

    public function store(Request $request, UserPermission $permission): RedirectResponse
    {
        $dto = AdditionCreateData::validateAndCreate($request->all());
        $this->createAdditionUseCase->execute($dto, $permission);
        return redirect()->back()->with('success', 'Услуга добавлена');
    }

    public function update(int $id, Request $request, UserPermission $permission): RedirectResponse
    {
        $dto = AdditionUpdateData::validateAndCreate($request->all());
        $this->updateAdditionUseCase->execute($id, $dto, $permission);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(int $id, UserPermission $permission): RedirectResponse
    {
        $this->removeAdditionUseCase->execute($id, $permission);
        return redirect()->back()->with('success', 'Услуга удалена');
    }

    public function groupList()
    {
        $list = $this->listGroupAdditionUseCase->execute();
        return response()->json($list, SymfonyResponse::HTTP_OK);
    }


}
