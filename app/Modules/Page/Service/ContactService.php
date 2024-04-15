<?php
declare(strict_types=1);

namespace App\Modules\Page\Service;

use App\Modules\Page\Entity\Contact;
use Illuminate\Http\Request;

class ContactService
{

    public function create(Request $request)
    {
        $contact = Contact::register(
            name: $request['name'],
            icon: $request['icon'],
            color: $request['color'],
            url: $request['url'],
            type: (int)$request['type']
        );

        return $contact;
    }

    public function update(Request $request, Contact $contact)
    {
        $contact->update([
            'name' => $request['name'],
            'icon' => $request['icon'],
            'color' => $request['color'],
            'url' => $request['url'],
            'type' => (int)$request['type']
        ]);
        $contact->refresh();
        return $contact;
    }

    public function destroy(Contact $contact)
    {
        if (!$contact->isDraft()) throw new \DomainException('Контакт опубликован, удалить нельзя');
        $contact->delete();
    }

    public function up(Contact $contact)
    {
        if ($contact->sort == 1) return;

        $new_sort = $contact->sort - 1;
        $old_sort = $contact->sort;
        /** @var Contact $new_contact */
        $new_contact = Contact::where('sort', $new_sort)->first();

        $new_contact->sort = $old_sort;
        $new_contact->save();

        $contact->sort = $new_sort;
        $contact->save();

    }

    public function down(Contact $contact)
    {
        if (Contact::count() == $contact->sort) return;

        $new_sort = $contact->sort + 1;
        $old_sort = $contact->sort;
        /** @var Contact $new_contact */
        $new_contact = Contact::where('sort', $new_sort)->first();

        $new_contact->sort = $old_sort;
        $new_contact->save();

        $contact->sort = $new_sort;
        $contact->save();
    }
}
