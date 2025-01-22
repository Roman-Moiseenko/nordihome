<?php

namespace App\Modules\Product\Service;

use App\Modules\Product\Entity\CategorySize;
use Illuminate\Http\Request;

class SizeService
{

    public function createCategory(Request $request): CategorySize
    {
        return CategorySize::register($request->string('name')->trim()->value());
    }
}
