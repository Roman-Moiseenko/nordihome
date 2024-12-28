<?php
declare(strict_types=1);

namespace App\Modules\Guide\Service;

use App\Modules\Guide\Entity\MarkingType;
use Illuminate\Http\Request;

class MarkingTypeService
{
    public function create(Request $request): MarkingType
    {
        return MarkingType::register($request->string('name')->trim()->value());
    }

    public function update(MarkingType $markingType, Request $request): void
    {
        $markingType->name = $request->string('name')->trim()->value();
        $markingType->save();
    }

    public function destroy(MarkingType $markingType): void
    {
        if ($markingType->products()->count() > 0) throw new \DomainException('Нельзя удалить, маркировка используется');
        $markingType->delete();
    }
}
