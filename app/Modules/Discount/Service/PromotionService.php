<?php
declare(strict_types=1);

namespace App\Modules\Discount\Service;

use App\Events\PromotionHasMoved;
use App\Modules\Discount\Entity\Promotion;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Product\Entity\Group;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Service\GroupService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PromotionService
{

    public function create(Request $request): Promotion
    {
        DB::transaction(function () use ($request, &$promotion) {

            $this->checkStartFinish($start, $finish, $request);

            $promotion = Promotion::register(
                $request->get('name'),
                $request->get('title') ?? '',
                $finish->format('Y-m-d'),
                $request->has('menu'),
                is_null($start) ? null : $start->format('Y-m-d'),
                $request->get('slug') ?? ''
            );

            $promotion->show_title = $request->has('show_title');
            $this->image($promotion, $request->file('image'));
            $this->icon($promotion, $request->file('icon'));

            $promotion->discount = $request->integer('discount');
            $promotion->description = $request->string('description')->trim()->value();
            $promotion->condition_url = $request->string('condition_url')->trim()->value();

            $promotion->save();
        });
        return $promotion;

    }

    public function update(Request $request, Promotion $promotion)
    {
        DB::transaction(function () use ($request, $promotion) {
            $promotion->update([
                'name' => $request->string('name')->trim()->value(),
                'title' => $request->string('title')->trim()->value(),
                'slug' => empty($request['slug']) ? Str::slug($request['name']) : $request['slug'],
                'menu' => $request->has('menu'),
                'description' => $request->string('description')->trim()->value(),
                'condition_url' => $request->string('condition_url')->trim()->value(),
                'show_title' => $request->has('show_title'),
            ]);

            $this->image($promotion, $request->file('image'));
            $this->icon($promotion, $request->file('icon'));
            $promotion->save();

            if (!$promotion->isPublished()) {
                $this->checkStartFinish($start, $finish, $request);
                $promotion->update([
                    'start_at' => is_null($start) ? null : $start->format('Y-m-d'),
                    'finish_at' => $finish->format('Y-m-d'),
                ]);
            }

            if ($promotion->discount != $request->integer('discount')) {
                $promotion->discount = $request->integer('discount');
                $promotion->save();
                foreach ($promotion->products as $product) {
                    $this->setPriceProduct($promotion, $product);
                }
            }
        });
    }

    public function add_product(Promotion $promotion, int $product_id): Promotion
    {
        $product = Product::find($product_id);
        if (!$promotion->isProduct($product->id)) {
            $promotion->products()->attach($product->id, ['price' => 0]);
            $this->setPriceProduct($promotion, $product);
            $promotion->refresh();
        } else {
            flash('Товар ' . $product->name . ' уже добавлен в акцию', 'warning');
        }
        //throw new \DomainException('Товар уже добавлен в акцию');
        return $promotion;
    }

    public function add_products(Promotion $promotion, string $textarea): Promotion
    {
        $list = explode("\r\n", $textarea);
        foreach ($list as $item) {
            $product = Product::whereCode($item)->first();
            if (!is_null($product)) {
                $this->add_product($promotion, $product->id);
            } else {
                flash('Товар с артикулом ' . $item . ' не найден', 'danger');
            }
        }
        return $promotion;
    }

    public function del_product(Product $product, Promotion $promotion)
    {
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
        if (empty($file)) return;
        $promotion->image->newUploadFile($file, 'image');
        $promotion->refresh();
    }

    public function icon(Promotion $promotion, $file): void
    {
        if (empty($file)) return;
        $promotion->icon->newUploadFile($file, 'icon');
        $promotion->refresh();
    }

    public function stop(Promotion $promotion)
    {
        if ($promotion->status() == Promotion::STATUS_STARTED) {
            $promotion->finish();
            $promotion->finish_at = now();
            $promotion->save();
            return;
        }
        throw new \DomainException('Нельзя остановить акцию');
    }

    public function published(Promotion $promotion)
    {
        if ($promotion->products()->count() == 0) {
            throw new \DomainException('В Акции нет товаров');
        }

        if ($promotion->status() == Promotion::STATUS_DRAFT) {
            $promotion->published();
            $promotion->save();
            return;
        }
        throw new \DomainException('Нельзя опубликовать акцию');
    }

    public function start(Promotion $promotion)
    {
        if ($promotion->status() == Promotion::STATUS_WAITING) {
            $promotion->start();
            if ($promotion->start_at == null || $promotion->start_at < now())
                $promotion->start_at = now();
            $promotion->save();
            event(new PromotionHasMoved($promotion));
            return;
        }
        throw new \DomainException('Нельзя запустить акцию');
    }

    public function finish(Promotion $promotion)
    {
        if ($promotion->status() == Promotion::STATUS_STARTED) {
            $promotion->finish();
            $promotion->save();
            event(new PromotionHasMoved($promotion));
            return;
        }
        throw new \DomainException('Ошибка завершения акции');
    }

    public function draft(Promotion $promotion)
    {
        if ($promotion->status() == Promotion::STATUS_WAITING) {
            $promotion->draft();
            $promotion->save();
            return;
        }
        throw new \DomainException('Нельзя отправить акцию в черновики');
    }

    public function set_product(Request $request, Promotion $promotion, Product $product)
    {
        $new_price = (int)$request['price'];
        $promotion->products()->updateExistingPivot($product->id, ['price' => $new_price]);
    }

    private function setPriceProduct(Promotion $promotion, Product $product)
    {
        $new_price = (int)ceil($product->getLastPrice() * (1 - $promotion->discount / 100));
        $promotion->products()->updateExistingPivot($product->id, ['price' => $new_price]);
    }

    /**
     * Проверяем дату начала акции, возвращаем даты начала и конца акции
     * @param $start
     * @param $finish
     * @return void
     */
    private function checkStartFinish(&$start, &$finish, Request $request): void
    {
        $start = empty($request['start']) ? null : Carbon::parse($request['start']);
        $finish = Carbon::parse($request['finish']);
        if (!is_null($start)) {
            if ($start->lte(now())) throw new \DomainException('Дата начала акции должна быть больше текущей');
            if ($finish->lte($start)) throw new \DomainException('Дата окончания акции должна быть больше даты начала');
        } else {
            if ($finish->lte(now())) throw new \DomainException('Дата окончания акции должна быть больше текущей');
        }
    }
}
