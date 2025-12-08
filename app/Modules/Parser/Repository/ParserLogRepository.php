<?php

namespace App\Modules\Parser\Repository;

use App\Modules\Parser\Entity\CategoryParser;
use App\Modules\Parser\Entity\ParserLog;
use App\Modules\Parser\Entity\ParserLogItem;
use Illuminate\Http\Request;

class ParserLogRepository
{

    public function getIndex(Request $request, &$filters)
    {
        $query = ParserLog::orderByDesc('date');
        $filters = [];

        if (count($filters) > 0) $filters['count'] = count($filters);

        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(ParserLog $log) => $this->LogToArray($log));
    }

    private function LogToArray(ParserLog $log): array
    {
        return array_merge($log->toArray(), [
            'new' => $log->items()->where('status', ParserLogItem::STATUS_NEW)->count(),
            'change' => $log->items()->where('status', ParserLogItem::STATUS_CHANGE)->count(),
            'del' => $log->items()->where('status', ParserLogItem::STATUS_DEL)->count(),
        ]);
    }

    public function LogWithToArray(ParserLog $log): array
    {
        return array_merge($log->toArray(), [
            'new' => $this->getItems($log, ParserLogItem::STATUS_NEW),
            'change' => $this->getItems($log, ParserLogItem::STATUS_CHANGE),
            'del' => $this->getItems($log, ParserLogItem::STATUS_DEL),
        ]);
    }

    private function getItems(ParserLog $log, int $status): array
    {
        return $log->items()->where('status', $status)
            ->get()
            ->map(fn(ParserLogItem $item) => $this->ItemToArray($item))->toArray();
    }

    private function ItemToArray(ParserLogItem $item): array
    {

        $data = [
            'product_id' => $item->parser->product_id,
            'code' => $item->parser->maker_id,
            'category' => $item->parser->product->category->getParentNames(),
            'category_parser' => array_map(function (CategoryParser $category) {
                return $category->getParentNames();
            }, $item->parser->categories()->getModels()),

        ];
        foreach ($item->data as $key => $value) {
            $data[$key] = $value;
        }



        return $data;
    }

    private function getBy()
    {

    }
}
