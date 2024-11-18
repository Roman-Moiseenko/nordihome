<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\Organization;
use App\Modules\Accounting\Entity\OrganizationContact;
use App\Modules\Accounting\Entity\OrganizationHolding;
use App\Modules\Accounting\Repository\OrganizationRepository;
use App\Modules\Accounting\Service\OrganizationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrganizationController extends Controller
{
    private OrganizationService $service;
    private OrganizationRepository $repository;

    public function __construct(OrganizationService $service, OrganizationRepository $repository)
    {
        $this->middleware(['auth:admin', 'can:accounting']);
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request)
    {

        $organizations = $this->repository->getIndex($request, $filters);//$this->pagination($query, $request, $pagination);
        $holdings = OrganizationHolding::orderBy('name')->getModels();
        //return view('admin.accounting.organization.index', compact('organizations', 'pagination'));

        return Inertia::render('Accounting/Organization/Index', [
            'organizations' => $organizations,
            'filters' => $filters,
            'holdings' => $holdings,
        ]);
    }

    public function create()
    {
        return view('admin.accounting.organization.create');
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

    public function show(Organization $organization)
    {
        return view('admin.accounting.organization.show', compact('organization'));
    }

/*
    public function edit(Organization $organization)
    {
        return view('admin.accounting.organization.edit', compact('organization'));
    }

    public function update(Request $request, Organization $organization)
    {
        $organization = $this->service->update($organization, $request);
        return redirect()->route('admin.accounting.organization.show', $organization);
    }
*/
    public function destroy(Organization $organization)
    {
        $this->service->delete($organization);
        return redirect()->route('admin.accounting.organization.index');
    }

    public function holdings()
    {
        $holdings = OrganizationHolding::orderBy('name')->get();
        return view('admin.accounting.organization.holdings', compact('holdings'));
    }

    public function add_contact(Request $request, Organization $organization)
    {
        $this->service->add_contact($organization, $request);
        return redirect()->back();
    }

    public function del_contact(OrganizationContact $contact)
    {
        $this->service->del_contact($contact);
        return redirect()->back();
    }

    public function set_contact(OrganizationContact $contact, Request $request)
    {
        $this->service->set_contact($contact, $request);
        return redirect()->back();
    }




}
