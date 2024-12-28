<?php
declare(strict_types=1);

namespace App\Modules\Guide\Service;

use App\Modules\Guide\Entity\Measuring;
use Illuminate\Http\Request;

class MeasuringService
{
    public function create(Request $request): Measuring
    {
        return Measuring::register($request->string('name')->trim()->value(), $request->boolean('fractional'));
    }

    public function update(Measuring $measuring, Request $request): void
    {
        $measuring->name = $request->string('name')->trim()->value();
        $measuring->fractional = $request->boolean('fractional');
        $measuring->save();
    }

    public function destroy(Measuring $measuring): void
    {
        if ($measuring->products()->count() > 0) throw new \DomainException('Нельзя удалить, ед.измерения используется');
        $measuring->delete();
    }
}
