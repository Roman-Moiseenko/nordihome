<?php

namespace App\Modules\Parser\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Parser\Entity\ParserLog;
use App\Modules\Parser\Repository\ParserLogRepository;
use App\Modules\Parser\Service\ParserLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ParserLogController extends Controller
{
    private ParserLogRepository $repository;
    private ParserLogService $service;

    public function __construct(
        ParserLogRepository $repository,
        ParserLogService    $service,
    )
    {
        $this->middleware(['auth:admin', 'can:product']);
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index(Request $request): Response
    {
        $logs = $this->repository->getIndex($request, $filters);
        return Inertia::render('Parser/Log/Index', [
            'logs' => $logs,
            'filters' => $filters,
        ]);
    }

    public function show(ParserLog $parser_log): Response
    {
        return Inertia::render('Parser/Log/Show', [
            'log' => $this->repository->LogWithToArray($parser_log),
        ]);
    }

    public function read(ParserLog $parser_log): RedirectResponse
    {
        $this->service->read($parser_log);

        return redirect()->back()->with('success', 'Прочитано');
    }
}
