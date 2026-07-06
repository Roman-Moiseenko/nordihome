<?php

namespace App\Modules\Parser\Service;

use App\Modules\Parser\Infrastructure\Models\ParserLog;
use App\Modules\Parser\Infrastructure\Models\ParserLogItem;

class ParserLogService
{


    public function read(ParserLog $log): void
    {
        $log->read_at = now();
        $log->staff_id = auth()->user()->profileable->id;
        $log->save();
    }
}
