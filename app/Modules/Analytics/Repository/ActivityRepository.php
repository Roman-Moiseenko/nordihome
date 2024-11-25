<?php
declare(strict_types=1);

namespace App\Modules\Analytics\Repository;

use App\Modules\Analytics\Entity\LoggerActivity;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class ActivityRepository
{
    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = LoggerActivity::orderByDesc('created_at');

        $filters = [];

        if ($request->integer('staff') > 0) {
            $staff_id = $request->integer('staff');
            $filters['staff'] = $staff_id;
            $query->where('user_id', $staff_id);
        }

        if (count($filters) > 0) $filters['count'] = count($filters);

        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(LoggerActivity $activity) => $this->ActivityToArray($activity));
    }

    public function ActivityToArray(LoggerActivity $activity): array
    {
        return array_merge($activity->toArray(), [
            'staff' => $activity->staff,
        ]);
    }
}
