<?php
declare(strict_types=1);

namespace App\Modules\Setting\Service;

use App\Modules\Product\Service\ProductService;
use App\Modules\Setting\Entity\Setting;
use Illuminate\Http\Request;

class SettingService
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function update(Request $request)
    {
        /** @var Setting $setting */
        $setting = Setting::where('slug', $request->string('slug')->value())->first();
        $data = $request->except(['slug','_method', '_token']);
        $setting->data = $data;
        $setting->save();
        if ($request->string('slug')->value() == 'parser') $this->productService->updateCostAllProductsIkea();
    }
}
