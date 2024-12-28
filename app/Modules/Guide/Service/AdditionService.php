<?php
declare(strict_types=1);

namespace App\Modules\Guide\Service;

use App\Modules\Guide\Entity\Addition;
use Illuminate\Http\Request;


class AdditionService
{

    public function destroy(Addition $addition): void
    {
        if ($addition->orderAdditions()->count() > 0) throw new \DomainException('Нельзя удалить, услуга используется');
        $addition->delete();
    }

    public function create(Request $request): Addition
    {
        $addition = Addition::register(
            $request->string('name')->trim()->value(),
            $request->integer('type'),
            $request->boolean('manual'),
            $request->integer('base')
        );
        $addition->class = $request->input('class');
        $addition->is_quantity = $request->boolean('is_quantity');
        $addition->save();
        return $addition;
    }

    public function update(Addition $addition, Request $request): void
    {
        $addition->name = $request->string('name')->trim()->value();
        $addition->class = $request->input('class');
        $addition->is_quantity = $request->boolean('is_quantity');
        //Не для автоматического расчета - фиксированное значение (base) или вручную каждый раз
        if (is_null($addition->class)) $addition->manual = $request->boolean('manual');
        $addition->base = $request->integer('base');
        $addition->save();
    }
}
