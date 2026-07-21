<?php

declare(strict_types=1);

namespace App\Modules\Feedback\Application\Actions\FormBack;

use App\Modules\Feedback\Application\DTOs\FormBack\FormBackIndexData;
use App\Modules\Feedback\Application\Interfaces\FormBackRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use DomainException;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class IndexFormBackUseCase
{
    public function __construct(
        private FormBackRepositoryInterface $formBackRepository,
    ) {}

    /**
     * @return LengthAwarePaginator<FormBackIndexData>
     */
    public function execute(UserPermission $userPermission): LengthAwarePaginator
    {
        if (!$userPermission->can('feedback.form.view')) {
            throw new DomainException('Доступ запрещён');
        }

        $paginator = $this->formBackRepository->getAll();

        return $paginator->through(fn(mixed $entity) => FormBackIndexData::fromEntity($entity));
    }
}
