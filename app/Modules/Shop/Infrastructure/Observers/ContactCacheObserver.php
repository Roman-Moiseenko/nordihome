<?php

namespace App\Modules\Shop\Infrastructure\Observers;

use App\Modules\Content\Entity\Contact;
use App\Modules\Shop\Infrastructure\Persistence\CacheInvalidationRegistry;

class ContactCacheObserver
{
    public function __construct(
        private CacheInvalidationRegistry $registry
    ) {}

    public function saved(Contact $contact): void
    {
        $this->registry->forgetContacts();
    }

    public function deleted(Contact $contact): void
    {
        $this->registry->forgetContacts();
    }
}