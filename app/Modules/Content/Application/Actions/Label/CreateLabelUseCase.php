<?php

namespace App\Modules\Content\Application\Actions\Label;

use App\Modules\Content\Application\DTOs\Label\LabelCreateData;
use App\Modules\Content\Domain\Entities\LabelEntity;
use App\Modules\Content\Domain\Interfaces\LabelRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use App\Modules\Shared\Domain\ValueObjects\Slug;

readonly class CreateLabelUseCase
{
    public function __construct(
        private LabelRepositoryInterface $labelRepository,
    ) {}

    public function execute(LabelCreateData $dto, UserPermission $userPermission): LabelEntity
    {
        if (!$userPermission->can('content.label.create')) {
            throw new AccessDeniedException();
        }

        $slug = new Slug($dto->slug ?: $dto->name);
        if ($this->labelRepository->existsSlug((string) $slug)) {
            $slug = new Slug((string) $slug . '-' . uniqid());
        }

        $label = new LabelEntity(
            name: $dto->name,
            slug: $slug,
        );

        return $this->labelRepository->save($label);
    }
}
