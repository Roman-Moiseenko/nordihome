<?php

namespace App\Console\Commands\Admin;

use App\Modules\Admin\Entity\Admin;
use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\Modification;
use App\Modules\Product\Entity\Product;
use Illuminate\Console\Command;

class DelModifCommand extends Command
{
    protected $signature = 'modif:del {id}';
    protected $description = 'Удаление модификации с товаром';

    public function handle()
    {
        \DB::transaction(function () {
            $id = $this->argument('id');
            $modification = Modification::find($id);
            $this->info($modification->name);

            foreach ($modification->products as $product) {
                $this->info($product->name);
                if (!is_null($product->parser)) {
                    $this->info('   <= Парсер');
                    $product->parser->delete();

                }
                $product->delete();
                $product->forceDelete();
            }
            $modification->delete();
            $this->info('УДАЛЕНО!');
        });


        return true;
    }
}
