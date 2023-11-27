<?php
declare(strict_types=1);

namespace App\Modules\Discount\Service;

use App\Modules\Discount\Entity\Promotion;
use App\Modules\Product\Entity\Group;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PromotionService
{

    public function create(Request $request): Promotion
    {
        $start = empty($request['start']) ? null : Carbon::parse($request['start']);
        $finish = Carbon::parse($request['finish']);

        if (!is_null($start)) {
            if ($start->lte(now())) throw new \DomainException('Дата начала акции должна быть больше текущей');
            if ($finish->lte($start)) throw new \DomainException('Дата окончания акции должна быть больше даты начала');
        } else {
            if ($finish->lte(now())) throw new \DomainException('Дата окончания акции должна быть больше текущей');
        }

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

        $promotion->description = $request['description'] ?? '';
        $promotion->condition_url = $request['condition_url'] ?? '';

        $promotion->save();
        return $promotion;

    }

    public function update(Request $request, Promotion $promotion)
    {

        $promotion->update([
            'name' => $request->get('name'),
            'title' => $request->get('title') ?? '',
            'slug' => empty($request['slug']) ? Str::slug($request['name']) : $request['slug'],
            'menu' => $request->has('menu'),
            'description' => $request['description'] ?? '',
            'condition_url' => $request['condition_url'] ?? '',
            'show_title' => $request->has('show_title'),
        ]);

        $this->image($promotion, $request->file('image'));
        $this->icon($promotion, $request->file('icon'));

        $promotion->save();

        if (!$promotion->isPublished()) {
            $start = empty($request['start']) ? null : Carbon::parse($request['start']);
            $finish = Carbon::parse($request['finish']);
            if (!is_null($start)) {
                if ($start->lte(now())) throw new \DomainException('Дата начала акции должна быть больше текущей');
                if ($finish->lte($start)) throw new \DomainException('Дата окончания акции должна быть больше даты начала');
            } else {
                if ($finish->lte(now())) throw new \DomainException('Дата окончания акции должна быть больше текущей');
            }

            $promotion->update([
                'start_at' => is_null($start) ? null : $start->format('Y-m-d'),
                'finish_at' => $finish->format('Y-m-d'),
            ]);
        }

        return $promotion;
    }

    public function add_group(Request $request, Promotion $promotion)
    {
        if (empty($request['group_id']) || empty($request['discount'])) throw new \DomainException('Не выбрана группа и/или не указана скидка');
        $group_id = (int)$request['group_id'];
        if (!$promotion->isGroup($group_id)) {
            $promotion->groups()->attach($group_id, ['discount' => (int)$request['discount']]);
            $promotion->refresh();
        } else {
            throw new \DomainException('Такая группа уже добавлена');
        }
    }

    public function del_group(Group $group, Promotion $promotion)
    {
        //$group_id = (int)$request['group_id'];
        $promotion->groups()->detach($group->id);
        $promotion->refresh();
    }

    public function set_group(Request $request, Promotion $promotion)
    {
        $group_id = (int)$request['group_id'];
        if ($promotion->isGroup($group_id)) {
            $promotion->groups()->updateExistingPivot($group_id, ['discount' => (int)$request['discount']]);
            $promotion->refresh();
        } else {
            throw new \DomainException('Такая группа не добавлена');
        }
    }

    public function delete(Promotion $promotion)
    {
        //TODO Проверка, если товар был продан по акции, то удалять нельзя
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

    //TODO для Cron-а ????


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
        if (count($promotion->products()) == 0) {
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
            return;
        }
        throw new \DomainException('Нельзя запустить акцию');
    }

    public function finish(Promotion $promotion)
    {
        if ($promotion->status() == Promotion::STATUS_STARTED) {
            $promotion->finish();
            $promotion->save();
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
}
