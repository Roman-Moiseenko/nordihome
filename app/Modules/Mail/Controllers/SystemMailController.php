<?php

namespace App\Modules\Mail\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Mail\Entity\SystemMail;
use App\Modules\Mail\Requests\SystemMailRequest;
use App\Modules\Mail\Repository\SystemMailRepository;
use App\Modules\Mail\Service\SystemMailService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SystemMailController extends Controller
{

    private SystemMailRepository $repository;
    private SystemMailService $service;

    public function __construct(SystemMailService $service, SystemMailRepository $repository)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $systemMails = $this->repository->getIndex($request, $filters);

        return Inertia::render('Mail/SystemMail/Index', [
                'systemMails' => $systemMails,
                'filters' => $filters,
                'mailables' => array_select(SystemMail::MAILABLES),
            ]
        );
    }

    public function show(SystemMail $system)
    {
        return Inertia::render('Mail/SystemMail/Show', [
                'mail' => $this->repository->SystemMailToArray($system),
            ]
        );
    }

    public function repeat(SystemMail $system)
    {
        $this->service->repeat($system);
        return redirect()->back()->with('success', 'Письмо было отправлено!');;
    }

    public function attachment(Request $request)
    {
        return response()->download(
            $request->string('file')->value());
    }
}
