<?php

declare(strict_types=1);

namespace App\Modules\Feedback\Application\Interfaces;

use App\Modules\Feedback\Domain\Entities\FormBackEntity;
use Illuminate\Pagination\LengthAwarePaginator;

interface FormBackRepositoryInterface
{
    public function save(FormBackEntity $formBack): FormBackEntity;

    public function getById(int $id): FormBackEntity;

    public function getAll(): LengthAwarePaginator;
}
