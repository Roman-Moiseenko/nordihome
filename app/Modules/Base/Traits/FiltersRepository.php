<?php
declare(strict_types=1);

namespace App\Modules\Base\Traits;

use Illuminate\Http\Request;

trait FiltersRepository
{
    //TODO Добавить новые фильтры и перенести во все Репозитории
    public function _comment(Request $request, &$filters, &$query): void
    {
        if ($request->string('comment') != '') {
            $comment = $request->string('comment')->trim()->value();
            $filters['comment'] = $comment;
            $query->where('comment', 'like', "%$comment%");
        }
    }

    public function _staff_id(Request $request, &$filters, &$query): void
    {
        if ($request->integer('staff_id') > 0) {
            $staff_id = $request->integer('staff_id');
            $filters['staff_id'] = $staff_id;
            $query->where('staff_id', $staff_id);
        }
    }

    public function _date_from(Request $request, &$filters, &$query): void
    {
        if (!is_null($begin = $request->date('date_from'))) {
            $filters['date_from'] = $begin->format('Y-m-d');
            $query->where('created_at', '>', $begin);
        }
    }

    public function _date_to(Request $request, &$filters, &$query): void
    {
        if (!is_null($end = $request->date('date_to'))) {
            $filters['date_to'] = $end->format('Y-m-d');
            $query->where('created_at', '<=', $end);
        }
    }

    public function _status(Request $request, &$filters, &$query): void
    {
        if (($status = $request->integer('status')) > 0) {
            $filters['status'] = $status;
            $query->where('status', $status);
        }
    }
}
