<?php

declare(strict_types=1);

namespace App\Modules\Content\Domain\Interfaces;

use App\Modules\Content\Domain\Entities\ContactEntity;

interface ContactRepositoryInterface
{
    /** @return ContactEntity[] */
    public function getAll(): array;

    public function getById(int $id): ContactEntity;

    public function save(ContactEntity $contact): ContactEntity;

    public function delete(int $id): void;

    public function move(int $id, int $position): void;
}
