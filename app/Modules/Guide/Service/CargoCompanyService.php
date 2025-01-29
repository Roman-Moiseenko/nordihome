<?php
declare(strict_types=1);

namespace App\Modules\Guide\Service;

use App\Modules\Guide\Entity\Addition;
use App\Modules\Guide\Entity\CargoCompany;
use Illuminate\Http\Request;

class CargoCompanyService
{

    public function destroy(CargoCompany $cargo): void
    {
        if ($cargo->deliveries()->count() > 0) throw new \DomainException('Нельзя удалить, Компания используется');
        $cargo->delete();
    }

    public function create(Request $request): CargoCompany
    {
        return CargoCompany::register(
            $request->string('name')->trim()->value(),
            $request->string('url')->trim()->value()
        );
    }

    public function update(CargoCompany $cargo, Request $request): void
    {
        $cargo->name = $request->string('name')->trim()->value();
        $cargo->url = $request->string('url')->trim()->value();
        $cargo->save();
    }
}
