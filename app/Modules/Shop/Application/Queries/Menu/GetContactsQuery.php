<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\Queries\Menu;

use App\Modules\Shop\Application\DTOs\Menu\ContactData;
use App\Modules\Shop\Infrastructure\Persistence\CacheInvalidationRegistry;
use App\Modules\Shop\Infrastructure\Persistence\Query\ContactQueryRepository;
use Illuminate\Support\Facades\Cache;

readonly class GetContactsQuery
{
    public function __construct(
        private ContactQueryRepository $repository
    )
    {
    }

    /**
     * @return array<string, ContactData>
     */
    public function execute(): array
    {

        $contacts = $this->repository->getPublishedContacts();

        $indexed = [];
        foreach ($contacts as $i => $contact) {
            $index = empty($contact->slug) ? $i : $contact->slug;
            $indexed[$index] = $contact;
        }

        return $indexed;
        /*
        return Cache::remember(
            CacheInvalidationRegistry::CONTACTS,
            now()->addDay(),
            function (): array {
                $contacts = $this->repository->getPublishedContacts();
                $indexed = [];
                foreach ($contacts as $contact) {
                    $indexed[$contact->slug] = $contact;
                }
                return $indexed;
            }
        );
        */
    }
}
