<?php
declare(strict_types=1);

namespace App\Modules\Page\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Page\Entity\Contact;
use App\Modules\Page\Service\ContactService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactController extends Controller
{
    private ContactService $service;

    public function __construct(ContactService $service)
    {
        $this->middleware(['auth:admin', 'can:options']);
        $this->service = $service;
    }

    public function index(Request $request): \Inertia\Response
    {
        $contacts = Contact::orderBy('sort')->get();
        return Inertia::render('Page/Contact/Index', [
            'contacts' => $contacts,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|min:3',
            'icon' => 'required|string|min:3',
            'url' => 'required|string|min:6',
        ]);
        $this->service->create($request);
        return redirect()->back()->with('success', 'Сохранено');
    }



    public function set_info(Request $request, Contact $contact): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|min:3',
            'icon' => 'required|string|min:3',
            'url' => 'required|string|min:6',
        ]);
        $this->service->setInfo($request, $contact);
        return redirect()->back()->with('success', 'Сохранено');
    }


    public function toggle(Contact $contact): RedirectResponse
    {
        if ($contact->isDraft()) {
            $message = 'Контакт опубликован на сайте';
            $contact->published();
        } else {
            $message = 'Контакт убран с сайта';
            $contact->draft();
        }

        return redirect()->back()->with('success', $message);
    }

    public function up(Contact $contact): RedirectResponse
    {
        $this->service->up($contact);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function down(Contact $contact): RedirectResponse
    {
        $this->service->down($contact);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(Contact $contact): RedirectResponse
    {
        $this->service->destroy($contact);
        return redirect()->back()->with('success', 'Контакт удален');
    }
}
