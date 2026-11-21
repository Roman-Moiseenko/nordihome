<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\Contact;

use App\Modules\Content\Application\DTOs\Contact\ContactCreateData;
use App\Modules\Content\Domain\Entities\ContactEntity;
use App\Modules\Content\Domain\Interfaces\ContactRepositoryInterface;
use App\Modules\Content\Domain\ValueObjects\ContactChannel;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use App\Modules\Shared\Domain\ValueObjects\Slug;

readonly class CreateContactUseCase
{
    public function __construct(
        private ContactRepositoryInterface $contactRepository,
    ) {
    }

    public function execute(ContactCreateData $dto, UserPermission $userPermission): ContactEntity
    {
        if (!$userPermission->can('content.contact.create')) {
            throw new AccessDeniedException();
        }

        $contact = new ContactEntity(
            name: $dto->name,
            slug: new Slug($dto->slug ?: $dto->name),
            icon: $dto->icon ?? '',
            color: $dto->color ?: '#000000',
            url: $dto->url,
            channel: $this->resolveChannel($dto->channel),
            svg: $dto->svg,
        );

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
