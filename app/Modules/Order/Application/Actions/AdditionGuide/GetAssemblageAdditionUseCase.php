<?php

namespace App\Modules\Order\Application\Actions\AdditionGuide;

use App\Modules\Guide\Domain\Entities\AdditionEntity;
use App\Modules\Guide\Domain\Interfaces\AdditionRepositoryInterface;

readonly class GetAssemblageAdditionUseCase
{

    public function __construct(
        private AdditionRepositoryInterface $repository
    )
    {
    }

    public function execute():? AdditionEntity
    {
        return $this->repository->findBySlug('assembly-15');
    }
}
