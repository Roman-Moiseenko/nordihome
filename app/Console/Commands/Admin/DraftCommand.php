<?php
declare(strict_types=1);

namespace App\Console\Commands\Admin;

use App\Modules\Catalog\Infrastructure\Models\Product;
use Illuminate\Console\Command;

class DraftCommand extends Command
{
    protected $signature = 'product:draft';
    protected $description = 'Товары без фото в черновик';
    public function handle()
    {
        /** @var Product[] $products */
        $products = Product::where('published', true)->getModels();
        foreach ($products as $product) {
            if (is_null($product->gallery)) {
                $product->published = false;
                $product->save();

                $this->info('Товар ' . $product->name . ' (' . $product->id .') отправлен в черновик!');
            }
        }

        return true;
    }
}
