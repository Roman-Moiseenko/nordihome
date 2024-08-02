<?php
declare(strict_types=1);

namespace App\Console\Commands\Admin;

use App\Modules\Product\Entity\Product;
use Illuminate\Console\Command;
use NotificationChannels\Telegram\TelegramUpdates;

class DraftCommand extends Command
{
    protected $signature = 'product:draft';
    protected $description = 'Товары без фото в черновик';
    public function handle()
    {
        /** @var Product[] $products */
        $products = Product::where('published', true)->getModels();
        foreach ($products as $product) {
            if (is_null($product->photo)) {
                $product->published = false;
                $product->save();

                $this->info('Товар ' . $product->name . ' отправлен в черновик!');
            }
        }

        return true;
    }
}
