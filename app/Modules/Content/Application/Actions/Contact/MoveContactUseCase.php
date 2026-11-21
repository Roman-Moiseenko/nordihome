<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\Contact;

use App\Modules\Content\Domain\Interfaces\ContactRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use DomainException;

readonly class MoveContactUseCase
{
    public function __construct(
        private ContactRepositoryInterface $contactRepository,
    ) {
    }

    public function execute(int $id, string $direction, UserPermission $userPermission): void
    {
        if (!$userPermission->can('content.contact.edit')) {
            throw new AccessDeniedException();
        }

        if (!in_array($direction, ['up', 'down'], true)) {
            throw new DomainException('Неверное направление перемещения');
        }

        $contacts = $this->contactRepository->getAll();

        $currentIndex = null;
        foreach ($contacts as $index => $contact) {
            if ($contact->id === $id) {
                $currentIndex = $index;
                break;
            }
        }

        if ($currentIndex === null) {
            return;
        }

        $newIndex = $direction === 'up' ? $currentIndex - 1 : $currentIndex + 1;
        $newIndex = max(0, min($newIndex, count($contacts) - 1));

        if ($newIndex === $currentIndex) {
            return;
        }

        $this->contactRepository->move($id, $newIndex);
    }
}
