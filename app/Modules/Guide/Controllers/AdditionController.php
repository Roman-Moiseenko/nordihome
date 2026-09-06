<?php
declare(strict_types=1);

namespace App\Modules\Guide\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Guide\Application\Actions\Addition\CreateAdditionUseCase;
use App\Modules\Guide\Application\Actions\Addition\RemoveAdditionUseCase;
use App\Modules\Guide\Application\Actions\Addition\UpdateAdditionUseCase;
use App\Modules\Guide\Application\DTOs\Addition\AdditionCreateData;
use App\Modules\Guide\Application\DTOs\Addition\AdditionUpdateData;
use App\Modules\Guide\Domain\ValueObjects\AdditionType;
use App\Modules\Guide\Infrastructure\Models\Addition;
use App\Modules\Guide\Service\AdditionService;
use App\Modules\Order\Entity\Addition\CalculateAddition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdditionController extends Controller
{

    public function __construct(
        private readonly CreateAdditionUseCase $createAdditionUseCase,
        private readonly UpdateAdditionUseCase $updateAdditionUseCase,
        private readonly RemoveAdditionUseCase $removeAdditionUseCase
    )
    {
    }

    public function index(): Response
    {
        $additions = Addition::orderBy('type')->get()->map(function (Addition $addition) {
            return array_merge($addition->toArray(), [
                'type_name' => AdditionType::TYPES[$addition->type],
                'class_name' => is_null($addition->class) ? '' : $addition->class::getName()
            ]);
        });
        $types = array_select(AdditionType::TYPES);
        $classes = array_select(CalculateAddition::CLASSES);

        return Inertia::render('Guide/Addition', [
            'additions' => $additions,
            'types' => $types,
            'classes' => $classes,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $dto = AdditionCreateData::validateAndCreate($request->all());
        $this->createAdditionUseCase->execute($dto);
        return redirect()->back()->with('success', 'Услуга добавлена');
    }

    public function update(int $id, Request $request): RedirectResponse
    {
        $dto = AdditionUpdateData::validateAndCreate($request->all());
        $this->updateAdditionUseCase->execute($id, $dto);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->removeAdditionUseCase->execute($id);
        return redirect()->back()->with('success', 'Услуга удалена');
    }


}
