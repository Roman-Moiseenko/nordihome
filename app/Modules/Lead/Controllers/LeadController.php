<?php

namespace App\Modules\Lead\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Lead\Entity\Lead;
use App\Modules\Lead\Entity\LeadStatus;
use App\Modules\Lead\Repository\LeadRepository;
use App\Modules\Lead\Service\LeadService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LeadController extends Controller
{
    private LeadService $service;
    private LeadRepository $repository;

    public function __construct(LeadService $service, LeadRepository $repository)
    {
        $this->middleware(['auth:admin']);
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request): Response
    {
        $leads = $this->repository->getIndex($request);
        $new_leads = $this->repository->getFreeLeads();
        $my_leads = $this->repository->getMyLeads();
        return Inertia::render('Lead/Dashboard', [
            'new_leads' => $new_leads,
            'my_leads' => $my_leads,
            //   'my_leads' => $my_leads,
            'boards' => LeadStatus::STATUSES

            //TODO Справочники, состояния и др.
        ]);
    }

    public function set_status(Lead $lead, Request $request): \Illuminate\Http\RedirectResponse
    {
        $result = $this->service->setStatus($lead, $request);

        return redirect()->back()->with('success', 'Обновлено!');
    }
}
