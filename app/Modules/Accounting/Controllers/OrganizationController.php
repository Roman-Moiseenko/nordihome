<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Controllers;


use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\Organization;
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
            'name' => 'required|string'
        ]);
        $this->service->create($request->all());
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
        $organization = $this->service->update($organization, $request->all());
        return redirect()->route('admin.accounting.organization.edit', $organization);
    }

    public function destroy(Organization $organization)
    {
        $this->service->delete($organization);
        return redirect()->route('admin.accounting.organization.index');
    }

}
