<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\Contact;

use App\Modules\Content\Domain\Interfaces\ContactRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class ToggleContactUseCase
{
    public function __construct(
        private ContactRepositoryInterface $contactRepository,
    ) {
    }

    public function execute(int $id, UserPermission $userPermission): string
    {
        if (!$userPermission->can('content.contact.edit')) {
            throw new AccessDeniedException();
        }

        $contact = $this->contactRepository->getById($id);
        $newPublished = !$contact->isPublished();

        $newPublished ? $contact->publish() : $contact->unpublish();
        $this->contactRepository->save($contact);

        return $newPublished
            ? 'Контакт опубликован на сайте'
            : 'Контакт убран с сайта';
    }
}
