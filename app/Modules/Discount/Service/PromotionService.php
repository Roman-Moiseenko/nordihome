<?php
declare(strict_types=1);

namespace App\Modules\Discount\Service;

use App\Modules\Discount\Entity\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PromotionService
{

    public function create(Request $request)
    {
        $promotion = Promotion::register(
            $request->get('name'),
            $request->get('title'),
            $request->get('finish'),
            $request->get('menu'),
            $request->get('start'),
            $request->get('slug')
        );

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
            'title' => $request->get('title'),
            'slug' => empty($request['slug']) ? Str::slug($request['name']) : $request['slug'],
            'menu' => $request->get('menu'),
            'description' => $request['description'] ?? '',
            'condition_url' => $request['condition_url'] ?? ''
        ]);

        if (!$promotion->isPublished()) {
            $promotion->update([
                'start_at' => $request->get('start'),
                'finish_at' => $request->get('finish'),
            ]);
        }

        $this->image($promotion, $request->file('image'));
        $this->icon($promotion, $request->file('icon'));

        $promotion->save();
        return $promotion;
    }

    public function add_group(Request $request, Promotion $promotion)
    {
        $group_id = (int)$request['group_id'];
        if (!$promotion->isGroup($group_id))
            $promotion->groups()->attach($group_id, ['discount' => (int)$request['discount']]);
    }

    public function del_group(Request $request, Promotion $promotion)
    {
        $group_id = (int)$request['group_id'];
        $promotion->groups()->detach($group_id);
    }

    public function delete(Promotion $promotion)
    {
        //TODO Проверка, если товар был продан по акции, то удалять нельзя
        Promotion::destroy($promotion->id);
    }


    public function start(Promotion $promotion)
    {
        if ($promotion->finish_at < time()) throw new \DomainException('Нельзя запустить акцию по завершению');
        $promotion->published();
    }

    public function finish(Promotion $promotion)
    {
        $promotion->finish();
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

}
