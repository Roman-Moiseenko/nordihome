<?php

declare(strict_types=1);

namespace App\Modules\Parser\Application\Interfaces;

use App\Modules\Parser\Domain\Entities\ParserProductEntity;


interface ParserProductRepositoryInterface
{
    public function getById(int $id): ParserProductEntity;

    public function getByCode(string $code): ?ParserProductEntity;

    public function save(ParserProductEntity $product): ParserProductEntity;

    public function delete(int $id): void;

}
