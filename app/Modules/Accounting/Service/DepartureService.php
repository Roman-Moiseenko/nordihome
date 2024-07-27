<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Events\DepartureHasCompleted;
use App\Modules\Accounting\Entity\DepartureDocument;
use App\Modules\Accounting\Entity\DepartureProduct;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Product\Entity\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepartureService
{
    private StorageService $storages;

    public function __construct(StorageService $storages)
    {
        $this->storages = $storages;
    }

    public function create(int $storage_id): DepartureDocument
    {
        /** @var Admin $manager */
        $manager = Auth::guard('admin')->user();
        //dd($storage_id);
        return DepartureDocument::register($storage_id, $manager->id);
    }

    public function update(Request $request, DepartureDocument $departure): DepartureDocument
    {
        if ($departure->isCompleted()) throw new \DomainException('Документ проведен. Менять данные нельзя');
        $departure->number = $request->string('number')->trim()->value();
        $departure->storage_id = $request->integer('storage_id');
        $departure->save();

        return $departure;
    }

    public function destroy(DepartureDocument $departure)
    {
        if ($departure->isCompleted()) throw new \DomainException('Документ проведен. Удалять нельзя');
        $departure->delete();
    }

    public function add(DepartureDocument $departure, int $product_id, int $quantity): ?DepartureDocument
    {
        if ($departure->isCompleted()) throw new \DomainException('Документ проведен. Менять данные нельзя');

        /** @var Product $product */
        $product = Product::find($product_id);
        if ($departure->isProduct($product_id)) {
            flash('Товар ' . $product->name . ' уже добавлен в документ', 'warning');
            return null;
        }

        $free_quantity = $departure->storage->getAvailable($product);
        $quantity = min($quantity, $free_quantity);

        //Добавляем в документ
        $departure->departureProducts()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'cost' => $product->getLastPrice()
        ]);
        $departure->refresh();
        return $departure;
    }

    public function add_products(DepartureDocument $departure, string $textarea): void
    {
        $list = explode("\r\n", $textarea);
        foreach ($list as $item) {
            $product = Product::whereCode($item)->first();
            if (!is_null($product)) {
                $this->add($departure, $product->id, 1);
            } else {
                flash('Товар с артикулом ' . $item . ' не найден', 'danger');
            }
        }
    }

    //Для AJAX
    public function set(Request $request, DepartureProduct $item): array
    {
        if ($item->document->isCompleted()) throw new \DomainException('Документ проведен. Менять данные нельзя');
        //Меняем данные
        $item->quantity = $request->integer('quantity');
        $item->save();
        return $item->document->getInfoData();
    }

    public function completed(DepartureDocument $departure)
    {
        //Проведение документа
        DB::transaction(function () use ($departure) {
            $this->storages->departure($departure->storage, $departure->departureProducts()->getModels());
            $departure->completed();
            event(new DepartureHasCompleted($departure));
        });
    }
}
