<?php
declare(strict_types=1);

namespace App\Modules\Guide\Service;

use App\Modules\Guide\Entity\VAT;
use Illuminate\Http\Request;

class VATService
{
    public function create(Request $request): VAT
    {
        return VAT::register($request->string('name')->trim()->value(), $request->integer('value'));
    }

    public function update(VAT $VAT, Request $request): void
    {
        $VAT->name = $request->string('name')->trim()->value();
        $VAT->value = $request->integer('value');
        $VAT->save();
    }

    public function destroy(VAT $VAT): void
    {
        if ($VAT->products()->count() > 0) throw new \DomainException('Нельзя удалить, налог используется');
        $VAT->delete();
    }
}
