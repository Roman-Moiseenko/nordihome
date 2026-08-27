<?php
declare(strict_types=1);

namespace App\Modules\Discount\Service;

use App\Events\PromotionHasMoved;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Discount\Infrastructure\Models\Promotion;
use App\Modules\Order\Infrastructure\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PromotionService
{
    public function create(Request $request): Promotion
    {
        return Promotion::register(
            $request->get('name'),
        );
    }
/*
    public function setInfo(Request $request, Promotion $promotion): void
    {
        $promotion->update([
            'name' => $request->string('name')->trim()->value(),
            'title' => $request->string('title')->trim()->value(),
            'slug' => empty($request['slug']) ? Str::slug($request['name']) : $request['slug'],
            'menu' => $request->boolean('menu'),
            'description' => $request->string('description')->trim()->value(),
            'condition_url' => $request->string('condition_url')->trim()->value(),
            'show_title' => $request->boolean('show_title'),
        ]);

        $promotion->saveImage($request->file('image'), $request->boolean('image_clear'));
        $promotion->saveIcon($request->file('icon'), $request->boolean('icon_clear'));


        if (!$promotion->isFinished()) {

            $this->checkStartFinish($start, $finish, $request);
            $promotion->update([
                'start_at' => is_null($start) ? null : $start->format('Y-m-d'),
                'finish_at' => is_null($finish) ? null : $finish->format('Y-m-d'),
            ]);

        }

        //Если изменилась скидка, пересчитать на весь товар
        if ($promotion->discount != $request->integer('discount')) {
            $promotion->discount = $request->integer('discount');
            $promotion->save();
            foreach ($promotion->products as $product) {
                $this->setPriceProduct($promotion, $product);
            }
        }
    }
*/
    public function addProduct(Promotion $promotion, int $product_id): void
    {
        $product = Product::find($product_id);
        if (!$promotion->isProduct($product->id)) {
            $new_price = (int)ceil($product->getPriceRetail() * (1 - $promotion->discount / 100));
            $promotion->products()->attach($product->id, ['price' => $new_price]);
        } else {
            throw new \DomainException('Товар ' . $product->name . ' уже добавлен в акцию');
        }
    }

    public function addProducts(Promotion $promotion, array $products): void
    {
        foreach ($products as $product) {
            $this->addProduct($promotion,
                $product['product_id'],
            );
        }
    }

    public function delProduct(Request $request, Promotion $promotion)
    {
        $product = Product::find($request->integer('product_id'));
        $promotion->products()->detach($product);
        $promotion->refresh();
    }

    public function delete(Promotion $promotion)
    {
        $count = OrderItem::where('discount_type', Promotion::class)->where('discount_id', $promotion->id)->count();
        if ($count > 0) throw new \DomainException('Нельзя удалить акцию, по которой были продажи');
        Promotion::destroy($promotion->id);
    }

    public function image(Promotion $promotion, $file): void
    {
        $promotion->saveImage($file);
        $promotion->refresh();
    }


    public function setProduct(Request $request, Promotion $promotion): void
    {
        $promotion->products()->updateExistingPivot(
            $request->integer('product_id'),
            ['price' => $request->integer('price')]
        );
    }

    private function setPriceProduct(Promotion $promotion, Product $product): void
    {
        $new_price = (int)ceil($product->getPrice() * (1 - $promotion->discount / 100));
        $promotion->products()->updateExistingPivot($product->id, ['price' => $new_price]);
    }

    /**
     * Проверяем дату начала акции, возвращаем даты начала и конца акции
     */
    private function checkStartFinish(&$start, &$finish, Request $request): void
    {
        $start = empty($request['start_at']) ? null : Carbon::parse($request['start_at']);
        $finish = empty($request['finish_at']) ? null : Carbon::parse($request['finish_at']);
        if (!is_null($start)) {
            if ($start->lte(now())) throw new \DomainException('Дата начала акции должна быть больше текущей');
            if (!is_null($finish) && $finish->lte($start)) throw new \DomainException('Дата окончания акции должна быть больше даты начала');
        }

        if (!is_null($finish) && $finish->lte(now())) throw new \DomainException('Дата окончания акции должна быть больше текущей');
    }
}
