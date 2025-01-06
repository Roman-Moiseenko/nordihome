<?php
declare(strict_types=1);

namespace App\Modules\Setting\Service;

use App\Modules\Product\Service\ProductService;
use App\Modules\Setting\Entity\Setting;
use App\Modules\Setting\Repository\SettingRepository;
use Illuminate\Http\Request;

class SettingService
{
    private ProductService $productService;
    private SettingRepository $repository;

    public function __construct(ProductService $productService, SettingRepository $repository)
    {
        $this->productService = $productService;
        $this->repository = $repository;
    }

    public function update(Request $request): void
    {
        $slug = $request->string('slug')->value();

        $setting = Setting::where('slug', $slug)->first();
        $data = $request->except(['slug','_method', '_token']);

        $setting->data = $data;
        $setting->save();
        if ($slug == 'parser') $this->productService->updateCostAllProductsIkea();
        if ($slug == 'mail') $this->saveMailBoxes();
    }



    private function saveMailBoxes(): void
    {
        $mail = $this->repository->getMail();
        $this->putPermanentEnv('MAIL_OUTBOX_USERNAME', $mail->outbox_name . '@' . $mail->mail_domain);
        $this->putPermanentEnv('MAIL_OUTBOX_PASSWORD', $mail->outbox_password);
        $this->putPermanentEnv('MAIL_SYSTEM_USERNAME', $mail->system_name . '@' . $mail->mail_domain);
        $this->putPermanentEnv('MAIL_SYSTEM_PASSWORD', $mail->system_password);
    }


    private function putPermanentEnv($key, $value): void
    {
        $path = app()->environmentFilePath();

        $escaped = preg_quote('='.env($key), '/');

        file_put_contents($path, preg_replace(
            "/^{$key}{$escaped}/m",
            "{$key}={$value}",
            file_get_contents($path)
        ));
    }
}
