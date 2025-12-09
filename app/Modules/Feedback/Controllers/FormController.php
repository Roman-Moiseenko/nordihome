<?php

namespace App\Modules\Feedback\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Feedback\Repository\FormRepository;
use App\Modules\Feedback\Service\FormService;
use App\Modules\Page\Entity\Widgets\FormWidget;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FormController extends Controller
{

    private FormService $service;
    private FormRepository $repository;

    public function __construct(FormService $service, FormRepository $repository)
    {
        $this->middleware(['auth:admin'])->except(['from_shop']);
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request)
    {

        $forms = $this->repository->getIndex($request, $filters);
        return Inertia::render('Feedback/Form/Index',
            [
                'forms' => $forms,
                'filters' => $filters
            ]);
    }

    public function from_shop(FormWidget $widget, Request $request): JsonResponse
    {
        //  try {
        $this->service->createForm($widget, $request);
        //Log::info(json_encode($request->all()));
        return \response()->json(true);
        /*   } catch (\Throwable $e) {
               Log::info($e->getMessage());
               Log::info($e->getFile());
               Log::info($e->getLine());
               return \response()->json(false);
           }
   */
    }

    public function get_url(FormWidget $widget): JsonResponse
    {
        return \response()->json(route('admin.feedback.form.from-shop', $widget));
    }
}
