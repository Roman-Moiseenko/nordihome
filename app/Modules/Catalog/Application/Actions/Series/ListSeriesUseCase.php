<?php

namespace App\Modules\Catalog\Application\Actions\Series;

use App\Modules\Catalog\Entity\Group;
use App\Modules\Catalog\Entity\Series;
use App\Modules\Shared\Application\DTOs\ListEntityData;

class ListSeriesUseCase
{
    public function execute(): array
    {
        $series = Series::orderBy('name')->getModels();

        return array_map(fn($seriey) => new ListEntityData(
            id: $seriey->id,
            name: $seriey->name,
            published: true,
        ), $series);
    }
}
