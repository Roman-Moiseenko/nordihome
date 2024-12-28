<?php
declare(strict_types=1);

namespace App\Modules\Guide\Service;

use App\Modules\Guide\Entity\Country;
use Illuminate\Http\Request;

class CountryService
{
    public function create(Request $request): Country
    {
        return Country::register($request->string('name')->trim()->value());
    }

    public function update(Country $country, Request $request): void
    {
        $country->name = $request->string('name')->trim()->value();
        $country->save();
    }

    public function destroy(Country $country): void
    {
        if ($country->products()->count() > 0) throw new \DomainException('Нельзя удалить, страна используется');
        $country->delete();
    }
}
