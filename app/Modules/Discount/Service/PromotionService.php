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
        return Promotion::register(
            $request->get('name'),
        );
    }

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

        } else {
           throw new \DomainException('Акция завершена, нельзя менять даты');
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
        $errors = [];
        foreach ($products as $product) {
            $_product = Product::whereCode($product['code'])->first();
            if (!is_null($_product)) {
                try {
                    $this->addProduct($promotion, $_product->id);
                } catch (\DomainException $e) {
                    //Глушим вывод уже добавленных товаров
                }
            } else {
                $errors[] = $product['code'];
            }
        }
        if (!empty($errors)) throw new \DomainException('Не найдены товары ' . implode(', ', $errors));
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

    public function stop(Promotion $promotion): void
    {
        if ($promotion->status() == Promotion::STATUS_STARTED) {
            $promotion->finish();
            $promotion->finish_at = now();
            $promotion->save();
            return;
        }
        throw new \DomainException('Нельзя остановить акцию');
    }

    public function published(Promotion $promotion): void
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

    public function start(Promotion $promotion): void
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

    public function finish(Promotion $promotion): void
    {
        if ($promotion->status() == Promotion::STATUS_STARTED) {
            $promotion->finish();
            $promotion->save();
            event(new PromotionHasMoved($promotion));
            return;
        }
        throw new \DomainException('Ошибка завершения акции');
    }

    public function draft(Promotion $promotion): void
    {
        if ($promotion->status() == Promotion::STATUS_WAITING) {
            $promotion->draft();
            $promotion->save();
            return;
        }
        throw new \DomainException('Нельзя отправить акцию в черновики');
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
