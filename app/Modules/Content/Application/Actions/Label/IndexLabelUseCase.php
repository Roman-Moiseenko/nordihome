<?php

namespace App\Modules\Content\Application\Actions\Label;

use App\Modules\Content\Application\DTOs\Label\LabelIndexData;
use App\Modules\Content\Domain\Entities\LabelEntity;
use App\Modules\Content\Domain\Interfaces\LabelPostRepositoryInterface;
use App\Modules\Content\Domain\Interfaces\LabelRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class IndexLabelUseCase
{
    public function __construct(
        private LabelRepositoryInterface $labelRepository,
        private LabelPostRepositoryInterface $labelPostRepository,
    ) {}

    public function execute(UserPermission $userPermission, int $perPage = 20): LengthAwarePaginator
    {
        if (!$userPermission->can('content.post.view')) {
            throw new AccessDeniedException();
        }

        $paginator = $this->labelRepository->paginate($perPage);

        $labelIds = $paginator->getCollection()
            ->map(fn(LabelEntity $label) => $label->id)
            ->toArray();

        $counts = $this->labelPostRepository->countPostsByLabelIds($labelIds);

        $dtos = $paginator->getCollection()->map(
            fn(LabelEntity $label) => LabelIndexData::fromEntity($label, $counts[$label->id] ?? 0)
        );
        $paginator->setCollection($dtos);

        return $paginator;
    }
}
