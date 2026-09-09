<?php

namespace App\Modules\Content\Application\Actions\Label;

use App\Modules\Content\Application\DTOs\Label\LabelUpdateData;
use App\Modules\Content\Domain\Entities\LabelEntity;
use App\Modules\Content\Domain\Interfaces\LabelRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Illuminate\Support\Str;

readonly class UpdateLabelUseCase
{
    public function __construct(
        private LabelRepositoryInterface $labelRepository,
    ) {}

    public function execute(int $labelId, LabelUpdateData $dto, UserPermission $userPermission): LabelEntity
    {
        if (!$userPermission->can('content.label.edit')) {
            throw new AccessDeniedException();
        }

        $label = $this->labelRepository->getById($labelId);

        $label->name = $dto->name;

        $slugString = $dto->slug !== null ? trim($dto->slug) : '';
        if ($slugString === '') {
            $slugString = Str::slug($label->name);
        }

        $slug = new Slug($slugString);
        if ($this->labelRepository->existsSlug((string) $slug, $labelId)) {
            $slug = new Slug((string) $slug . '-' . uniqid());
        }
        $label->slug = $slug;

        return $this->labelRepository->save($label);
    }
}
