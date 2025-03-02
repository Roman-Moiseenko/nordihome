<?php

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\PaymentDecryption;
use App\Modules\Accounting\Entity\PaymentDocument;
use App\Modules\Accounting\Entity\SupplyDocument;
use App\Modules\Accounting\Entity\Trader;
use App\Modules\Accounting\Repository\PaymentDocumentRepository;
use App\Modules\Accounting\Repository\TraderRepository;
use App\Modules\Accounting\Service\PaymentDocumentService;
use App\Modules\Admin\Repository\StaffRepository;
use App\Modules\Order\Entity\Order\OrderPayment;
use DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    private PaymentDocumentService $service;
    private PaymentDocumentRepository $repository;
    private TraderRepository $traders;
    private StaffRepository $staffs;

    public function __construct(
        PaymentDocumentService    $service,
        PaymentDocumentRepository $repository,
        TraderRepository          $traders,
        StaffRepository           $staffs,
    )
    {
        $this->middleware(['auth:admin', 'can:accounting']);
        $this->middleware(['auth:admin', 'can:admin-panel'])->only(['work', 'destroy']);
        $this->service = $service;
        $this->repository = $repository;
        $this->traders = $traders;
        $this->staffs = $staffs;
    }

    public function index(Request $request): Response
    {
        $payments = $this->repository->getIndex($request, $filters);
        $distributors = Distributor::orderBy('name')->getModels();
        $staffs = $this->staffs->getStaffsChiefs();
        $traders = $this->traders->getTraders();
        return Inertia::render('Accounting/Payment/Index', [
            'payments' => $payments,
            'filters' => $filters,
            'traders' => $traders,
            'distributors' => $distributors,
            'staffs' => $staffs,
        ]);
    }

    public function show(PaymentDocument $payment): Response
    {
        $traders = $this->traders->getTraders();
        return Inertia::render('Accounting/Payment/Show', [
            'payment' => $this->repository->PaymentWithToArray($payment),
            'payers' => $traders,
        ]);
    }

    public function destroy(PaymentDocument $payment): RedirectResponse
    {
        $this->service->destroy($payment);
        return redirect()->back()->with('success', 'Платежный документ помечен на удаление');
    }

    public function restore(PaymentDocument $payment): RedirectResponse
    {
        $this->service->restore($payment);
        return redirect()->back()->with('success', 'Платежный документ восстановлен');
    }

    public function full_destroy(PaymentDocument $payment): RedirectResponse
    {
        $this->service->fullDestroy($payment);
        return redirect()->back()->with('success', 'Платежный документ удален окончательно');
    }

    public function completed(PaymentDocument $payment): RedirectResponse
    {
        $this->service->completed($payment);
        return redirect()->back()->with('success', 'Документ проведен');
    }

    public function work(PaymentDocument $payment): RedirectResponse
    {
        $this->service->work($payment);
        return redirect()->back()->with('success', 'Документ возвращен в работу');
    }

    public function set_info(PaymentDocument $payment, Request $request): RedirectResponse
    {
        $this->service->setInfo($payment, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function not_paid(PaymentDocument $payment, Request $request): RedirectResponse
    {
        $this->service->notPaid($payment);
        return redirect()->back()->with('success', 'Добавлено');
    }

    public function set_amount(PaymentDecryption $decryption, Request $request): RedirectResponse
    {
        $this->service->setAmount($decryption, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

}
