<?php

namespace App\Modules\Parser\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Parser\Application\Actions\ParserLog\IndexParserLogUseCase;
use App\Modules\Parser\Application\Actions\ParserLog\ReadParserLogUseCase;
use App\Modules\Parser\Application\Actions\ParserLog\ViewParserLogUseCase;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ParserLogController extends Controller
{
    public function __construct(
        private readonly IndexParserLogUseCase $indexParserLogUseCase,
        private readonly ViewParserLogUseCase $viewParserLogUseCase,
        private readonly ReadParserLogUseCase $readParserLogUseCase,
    ) {}

    public function index(Request $request, UserPermission $userPermission): Response
    {
        $perPage = (int) $request->input('size', 20);
        $logs = $this->indexParserLogUseCase->execute($userPermission, $perPage);

        return Inertia::render('Parser/Log/Index', [
            'logs' => $logs,
        ]);
    }

    public function show(int $id, UserPermission $userPermission): Response
    {
        $log = $this->viewParserLogUseCase->execute($id, $userPermission);

        return Inertia::render('Parser/Log/Show', [
            'log' => $log,
        ]);
    }

    public function read(int $id, UserPermission $userPermission): RedirectResponse
    {
        $staffId = auth()->user()->profileable->id;

        $this->readParserLogUseCase->execute($id, $staffId, $userPermission);

        return redirect()->back()->with('success', 'Прочитано');
    }
}
