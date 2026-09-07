<?php

namespace App\Modules\Order\Application\Actions\AdditionGuide;

use App\Modules\Guide\Domain\Entities\AdditionEntity;
use App\Modules\Guide\Domain\Interfaces\AdditionRepositoryInterface;

readonly class GetDeliveryAdditionUseCase
{
    public function __construct(
        private AdditionRepositoryInterface $repository
    ){}

    public function execute(int $regionCode): AdditionEntity
    {
        if ($regionCode == 39) return $this->repository->findBySlug('koenig');

        return $this->repository->findBySlug('russia');
    }
}
