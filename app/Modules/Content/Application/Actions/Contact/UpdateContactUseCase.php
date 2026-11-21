<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\Contact;

use App\Modules\Content\Application\DTOs\Contact\ContactUpdateData;
use App\Modules\Content\Domain\Entities\ContactEntity;
use App\Modules\Content\Domain\Interfaces\ContactRepositoryInterface;
use App\Modules\Content\Domain\ValueObjects\ContactChannel;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use App\Modules\Shared\Domain\ValueObjects\Slug;

readonly class UpdateContactUseCase
{
    public function __construct(
        private ContactRepositoryInterface $contactRepository,
    ) {
    }

    public function execute(int $id, ContactUpdateData $dto, UserPermission $userPermission): ContactEntity
    {
        if (!$userPermission->can('content.contact.edit')) {
            throw new AccessDeniedException();
        }

        $contact = $this->contactRepository->getById($id);

        if ($dto->name !== null) $contact->name = $dto->name;
        if ($dto->slug !== null) $contact->slug = new Slug($dto->slug);
        if ($dto->icon !== null) $contact->icon = $dto->icon;
        if ($dto->color !== null) $contact->color = $dto->color;
        if ($dto->url !== null) $contact->url = $dto->url;
        if ($dto->svg !== null) $contact->svg = $dto->svg;
        if ($dto->channel !== null) $contact->channel = $this->resolveChannel($dto->channel);

        return $this->contactRepository->save($contact);
    }

    private function resolveChannel(?string $channel): ?ContactChannel
    {
        if ($channel === null || trim($channel) === '') {
            return null;
        }

        return ContactChannel::from($channel);
    }
}
