<?php

namespace App\Modules\Feedback\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Content\Entity\Widgets\FormWidget;
use App\Modules\Feedback\Application\Actions\FormBack\CreateFormBackUseCase;
use App\Modules\Feedback\Application\Actions\FormBack\IndexFormBackUseCase;
use App\Modules\Feedback\Application\DTOs\FormBack\FormBackCreateData;
use App\Modules\Feedback\Repository\FormRepository;
use App\Modules\Feedback\Service\FormService;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FormController extends Controller
{


    private FormRepository $repository;

    public function __construct(
        FormRepository $repository,
        public IndexFormBackUseCase  $indexFormBackUseCase,
        public CreateFormBackUseCase $createFormBackUseCase,
    )
    {

        $this->repository = $repository;
    }

    public function index(Request $request, UserPermission $userPermission)
    {
        $forms = $this->indexFormBackUseCase->execute($userPermission);
        return Inertia::render('Feedback/Form/Index',
            [
                'forms' => $forms,
            ]);
    }

    public function from_shop(FormWidget $widget, Request $request): JsonResponse
    {
        $dto = FormBackCreateData::validateAndCreate($request->all());
        $this->createFormBackUseCase->execute($dto);
        return \response()->json(true);
    }

    public function feedback(Request $request)
    {
        $dto = FormBackCreateData::validateAndCreate($request->all());
        $this->createFormBackUseCase->execute($dto);
        return \response()->json(true);
    }

    public function get_url(FormWidget $widget): JsonResponse
    {
        return \response()->json(route('admin.feedback.form.from-shop', $widget));
    }
}
