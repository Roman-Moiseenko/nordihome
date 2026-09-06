<?php

namespace App\Modules\Guide\Application\Actions\Addition;

use App\Modules\Guide\Domain\Interfaces\AdditionRepositoryInterface;

class RemoveAdditionUseCase
{

    public function __construct(private AdditionRepositoryInterface $repository)
    {

    }

    public function execute(int $id)
    {

        //TODO Проверка на наличие услуг

        $this->repository->remove($id);
    }
}
