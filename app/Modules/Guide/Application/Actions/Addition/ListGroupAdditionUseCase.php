<?php

namespace App\Modules\Guide\Application\Actions\Addition;

use App\Modules\Guide\Application\DTOs\Addition\AdditionViewData;
use App\Modules\Guide\Domain\Interfaces\AdditionRepositoryInterface;
use App\Modules\Guide\Domain\ValueObjects\AdditionType;


readonly class ListGroupAdditionUseCase
{
    public function __construct(
        private AdditionRepositoryInterface $repository
    )
    {

    }

    public function execute()
    {
        return array_map(
            fn($type) => [
                'label' => AdditionType::TYPES[$type],
                'additions' => AdditionViewData::collect($this->repository->getByType($type))

            ],
            array_keys(AdditionType::TYPES)
        );
    }
}
