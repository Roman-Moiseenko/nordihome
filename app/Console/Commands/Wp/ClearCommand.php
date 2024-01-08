<?php
declare(strict_types=1);

namespace App\Console\Commands\Wp;

use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use Illuminate\Console\Command;

class ClearCommand extends Command
{
    protected $signature = 'wp:clear {catalog} {product}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Очистка данных';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $catalog_id = $this->argument('catalog');
        $product_id = $this->argument('product');
        $this->clear_product($product_id);
        $this->clear_category($catalog_id);
        return true;
    }

    private function clear_category($catalog_id)
    {
        $this->warn('Очистка каталогов');
        Category::where('id', '>', $catalog_id)->update(['parent_id' => null]);
        $categories = Category::where('id', '>', $catalog_id)->get();
        foreach ($categories as $category) {
            $category->image->delete();
        }
        Category::where('id', '>', $catalog_id)->delete();
        $this->info('Очистка Завершена');
    }


    private function clear_product($product_id)
    {
        $this->warn('Очистка товаров');
        $products = Product::where('id', '>', $product_id)->get();
        foreach ($products as $product) {
            foreach ($product->photos as $photo) {
                $photo->delete();
            }
        }
        Product::where('id', '>', $product_id)->delete();
        $this->info('Очистка Завершена');
    }
}
