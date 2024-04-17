<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Page;

use App\Http\Controllers\Controller;
use App\Modules\Page\Entity\Contact;
use App\Modules\Page\Service\ContactService;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    private ContactService $service;

    public function __construct(ContactService $service)
    {
        $this->middleware(['auth:admin', 'can:options']);
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $contacts = Contact::orderBy('sort')->get();
            return view('admin.page.contact.index', compact('contacts'));
        });
    }

    public function create()
    {
        return $this->try_catch_admin(function () {

            return view('admin.page.contact.create');
        });
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:3',
            'icon' => 'required|string|min:3',
            'url' => 'required|string|min:6',
        ]);
        return $this->try_catch_admin(function () use($request) {
            $contact = $this->service->create($request);
            return redirect()->route('admin.page.contact.index');
        });
    }
/*
    public function show(Contact $contact)
    {
        return $this->try_catch_admin(function () use($contact) {
            return view('admin.page.contact.show', compact('contact'));
        });
    }
*/
    public function edit(Contact $contact)
    {
        return $this->try_catch_admin(function () use($contact) {
            return view('admin.page.contact.edit', compact('contact'));
        });
    }

    public function update(Request $request, Contact $contact)
    {
        $request->validate([
            'name' => 'required|string|min:3',
            'icon' => 'required|string|min:3',
            'url' => 'required|string|min:6',
        ]);
        return $this->try_catch_admin(function () use($request, $contact) {
            $this->service->update($request, $contact);
            return redirect()->route('admin.page.contact.index');
        });
    }


    public function draft(Contact $contact)
    {
        return $this->try_catch_admin(function () use($contact) {
            $contact->draft();
            return redirect()->back();
        });
    }

    public function published(Contact $contact)
    {
        return $this->try_catch_admin(function () use($contact) {
            $contact->published();
            return redirect()->back();
        });
    }

    public function up(Contact $contact)
    {
        return $this->try_catch_admin(function () use($contact) {
            $this->service->up($contact);
            return redirect()->back();
        });
    }

    public function down(Contact $contact)
    {
        return $this->try_catch_admin(function () use($contact) {
            $this->service->down($contact);
            return redirect()->back();
        });
    }

    public function destroy(Contact $contact)
    {
        return $this->try_catch_admin(function () use($contact) {
            $this->service->destroy($contact);
            return redirect()->route('admin.page.contact.index');
        });
    }
}
