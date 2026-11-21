<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\Contact;

use App\Modules\Content\Domain\Interfaces\ContactRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use DomainException;

readonly class RemoveContactUseCase
{
    public function __construct(
        private ContactRepositoryInterface $contactRepository,
    ) {
    }

    public function execute(int $id, UserPermission $userPermission): void
    {
        if (!$userPermission->can('content.contact.delete')) {
            throw new AccessDeniedException();
        }

        $contact = $this->contactRepository->getById($id);

        if ($contact->isPublished()) {
            throw new DomainException('Контакт опубликован, удалить нельзя');
        }

        $this->contactRepository->delete($id);
    }
}
