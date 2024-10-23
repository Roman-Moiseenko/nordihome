<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\Organization;
use App\Modules\Accounting\Entity\OrganizationContact;
use App\Modules\Accounting\Entity\OrganizationHolding;
use App\Modules\Accounting\Service\OrganizationService;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    private OrganizationService $service;

    public function __construct(OrganizationService $service)
    {
        $this->middleware(['auth:admin', 'can:accounting']);
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $query = Organization::orderBy('created_at');
        $organizations = $this->pagination($query, $request, $pagination);
        return view('admin.accounting.organization.index', compact('organizations', 'pagination'));
    }

    public function create()
    {
        return view('admin.accounting.organization.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'inn' => 'required'
        ]);
        $this->service->create($request);
        return redirect()->route('admin.accounting.organization.index');
    }

    public function show(Organization $organization)
    {
        return view('admin.accounting.organization.show', compact('organization'));
    }

    public function edit(Organization $organization)
    {
        return view('admin.accounting.organization.edit', compact('organization'));
    }

    public function update(Request $request, Organization $organization)
    {
        $organization = $this->service->update($organization, $request);
        return redirect()->route('admin.accounting.organization.edit', $organization);
    }

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
