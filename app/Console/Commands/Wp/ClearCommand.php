<?php
declare(strict_types=1);

namespace App\Console\Commands\Wp;

use App\Modules\Catalog\Entity\Product;
use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Catalog\Infrastructure\Models\Room;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class ClearCommand extends Command
{

    use ConfirmableTrait;

    protected $signature = 'wp:clear
        {--catalog= : 1 / "Да" } {--product= : 1 / "Да" } {--room= : 1 / "Да" }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Очистка данных';

    /**
     * Execute the console command.
     */
    public function handle(): bool
    {
        if (! $this->confirmToProceed()) {
            return false;
        }

        $catalog = $this->option('catalog');
        $product = $this->option('product');
        $room = $this->option('room');

        //dd($catalog, $product, $room);

        if (!is_null($product)) $this->clear_product();
        if (!is_null($catalog)) $this->clear_category();
        if (!is_null($room)) $this->clear_room();
        return true;
    }

    private function clear_category(): void
    {
        $this->warn('Очистка каталогов');
        Category::where('id', '>', 0)->update(['parent_id' => null]);
        $categories = Category::getModels();
        $this->info('Каталогов - ' . count($categories));
        foreach ($categories as $category) {
            $category->image->delete();
        }
        Category::where('id', '>', 0)->delete();
        $this->info('Очистка Завершена');
    }


    private function clear_room(): void
    {
        $this->warn('Очистка комнат');
        Room::where('id', '>', 0)->update(['parent_id' => null]);
        $rooms = Room::getModels();
        $this->info('Каталогов - ' . count($rooms));
        foreach ($rooms as $room) {
            $room->image->delete();
        }
        Room::where('id', '>', 0)->delete();
        $this->info('Очистка Завершена');
    }

    private function clear_product(): void
    {
        $this->warn('Очистка товаров');
        $products = Product::getModels();
        $this->info('Товаров - ' . count($products));
        foreach ($products as $product) {
            foreach ($product->gallery as $photo) {
                $photo->delete();
            }
        }
        Product::where('id', '>', 0)->delete();
        $this->info('Очистка Завершена');
    }
}
