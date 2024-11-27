<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\Organization;
use App\Modules\Accounting\Entity\OrganizationContact;
use App\Modules\Accounting\Entity\OrganizationHolding;
use App\Modules\Accounting\Repository\OrganizationRepository;
use App\Modules\Accounting\Service\OrganizationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OrganizationController extends Controller
{
    private OrganizationService $service;
    private OrganizationRepository $repository;

    public function __construct(OrganizationService $service, OrganizationRepository $repository)
    {
        //$this->middleware(['auth:admin', 'can:accounting']);
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request): Response
    {
        $organizations = $this->repository->getIndex($request, $filters);//$this->pagination($query, $request, $pagination);
        $holdings = OrganizationHolding::orderBy('name')->getModels();
        return Inertia::render('Accounting/Organization/Index', [
            'organizations' => $organizations,
            'filters' => $filters,
            'holdings' => $holdings,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'inn' => 'required|min:10|max:12',
        ]);
        try {
            $organization = $this->service->create_find(
                $request->string('inn')->value(),
                $request->string('bik')->value(),
                $request->string('account')->value(),
            );
            return redirect()->route('admin.accounting.organization.show', $organization)->with('success', 'Организация добавлена');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show(Organization $organization): Response
    {
        $holdings = OrganizationHolding::orderBy('name')->getModels();
        return Inertia::render('Accounting/Organization/Show', [
            'organization' => $this->repository->OrganizationWithToArray($organization),
            'holdings' => $holdings,
        ]);
    }

    public function update(Organization $organization): RedirectResponse
    {
        try {
            $this->service->update_find($organization);
            return redirect()->route('admin.accounting.organization.show', $organization)->with('success', 'Данные обновлены');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    public function del_contact(OrganizationContact $contact)
    {
        try {
            $contact->delete();
            return redirect()->back()->with('success', 'Удалено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function set_contact(Organization $organization, Request $request)
    {
        try {
            $this->service->setContact($organization, $request);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function set_info(Request $request, Organization $organization): RedirectResponse
    {
        try {
            $this->service->setInfo($organization, $request);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function search_add(Request $request): JsonResponse
    {
        $result = $this->repository->search($request->string('search')->trim()->value());
        return response()->json($result);
    }

    public function find(Request $request): JsonResponse
    {
        try {
            if ($request->boolean('foreign')) {
                $organization = $this->service->create_foreign($request);
            } else {
                $organization = $this->service->create_find(
                    $request->string('inn')->value(),
                    $request->string('bik')->value(),
                    $request->string('account')->value(),
                );
            }
            return response()->json($organization->id);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function upload(Organization $organization, Request $request): RedirectResponse
    {
        try {
            $this->service->upload($organization, $request);
            return redirect()->back()->with('success', 'Файл загружен');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


}
