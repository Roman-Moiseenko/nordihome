<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\SizeGrid;

use App\Modules\Catalog\Application\DTOs\SizeGrid\SizeGridCreateData;
use App\Modules\Catalog\Domain\Entities\SizeGridEntity;
use App\Modules\Catalog\Domain\Interfaces\SizeGridRepositoryInterface;
use App\Modules\Catalog\Domain\ValueObjects\SizeGridDimension;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class CreateSizeGridUseCase
{
    public function __construct(
        private SizeGridRepositoryInterface $sizeGridRepository,
    ) {
    }

    public function execute(SizeGridCreateData $dto, UserPermission $userPermission): SizeGridEntity
    {
        if (!$userPermission->can('catalog.product.create')) {
            throw new AccessDeniedException();
        }

        $grid = new SizeGridEntity(name: trim($dto->name));
        $grid->dimensions = $this->buildDimensions($dto->dimensions);

        return $this->sizeGridRepository->save($grid);
    }

    /**
     * @param array<int, string|array<string, mixed>> $dimensions
     * @return SizeGridDimension[]
     */
    private function buildDimensions(array $dimensions): array
    {
        $result = [];

        foreach ($dimensions as $dimension) {
            $vo = $this->resolveDimension($dimension);

            if ($vo !== null) {
                $result[$vo->code] = $vo;
            }
        }

        return array_values($result);
    }

    private function resolveDimension(string|array $dimension): ?SizeGridDimension
    {
        if (is_string($dimension)) {
            return SizeGridDimension::findByCode($dimension);
        }

        $code = (string) ($dimension['code'] ?? '');
        if ($code === '') {
            return null;
        }

        $known = SizeGridDimension::findByCode($code);

        return $known ?? new SizeGridDimension(
            code: $code,
            name: (string) ($dimension['name'] ?? $code),
        );
    }
}
