<?php

namespace App\Modules\Parser\Service;

use App\Modules\Parser\Entity\ParserLog;
use App\Modules\Parser\Entity\ParserLogItem;

class ParserLogService
{

    public function addLog(int $parser_id, int $status, array $data = []): void
    {
        $date = now()->toDateString();
        if (is_null($log = ParserLog::findDate($date))) {
            $log = ParserLog::register();
        }
        $log->items()->save(ParserLogItem::new($status, $parser_id, $data));
    }

    public function read(ParserLog $log): void
    {
        $log->read_at = now();
        $log->read = true;
        $log->staff_id = \Auth::guard('admin')->user()->id;
        $log->save();
    }
}
