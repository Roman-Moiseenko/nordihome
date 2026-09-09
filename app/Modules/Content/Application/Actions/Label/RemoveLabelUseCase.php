<?php

namespace App\Modules\Content\Application\Actions\Label;

use App\Modules\Content\Domain\Interfaces\LabelPostRepositoryInterface;
use App\Modules\Content\Domain\Interfaces\LabelRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class RemoveLabelUseCase
{
    public function __construct(
        private LabelRepositoryInterface $labelRepository,
        private LabelPostRepositoryInterface $labelPostRepository,
    ) {}

    public function execute(int $labelId, UserPermission $userPermission): void
    {
        if (!$userPermission->can('content.label.delete')) {
            throw new AccessDeniedException();
        }

        if ($this->labelPostRepository->countPostsByLabelId($labelId) > 0) {
            throw new \DomainException('Нельзя удалить метку с записями');
        }

        $this->labelRepository->delete($labelId);
    }
}
