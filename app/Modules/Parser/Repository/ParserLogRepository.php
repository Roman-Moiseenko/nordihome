<?php

namespace App\Modules\Parser\Repository;

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
            'new' => $log->items()->where('status', ParserLogItem::STATUS_NEW)->get()->toArray(),
            'change' => $log->items()->where('status', ParserLogItem::STATUS_CHANGE)->get()->toArray(),
            'del' => $log->items()->where('status', ParserLogItem::STATUS_DEL)->get()->toArray(),
        ]);
    }


    private function getBy()
    {

    }
}
