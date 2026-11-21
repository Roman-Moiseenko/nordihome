<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\Contact;

use App\Modules\Content\Application\DTOs\Contact\ContactIndexData;
use App\Modules\Content\Domain\Entities\ContactEntity;
use App\Modules\Content\Domain\Interfaces\ContactRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class IndexContactQuery
{
    public function __construct(
        private ContactRepositoryInterface $contactRepository,
    ) {
    }

    /**
     * @return ContactIndexData[]
     */
    public function execute(UserPermission $userPermission): array
    {
        if (!$userPermission->can('content.contact.view')) {
            throw new AccessDeniedException();
        }

        return array_map(
            fn(ContactEntity $contact) => ContactIndexData::fromEntity($contact),
            $this->contactRepository->getAll()
        );
    }
}
