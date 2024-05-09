<?php
declare(strict_types=1);

namespace App\Modules\Admin\Repository;

use App\Modules\Admin\Entity\Admin;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Entity\Worker;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\ExpectedValues;

class WorkerRepository
{
  /*  public function getStaffsByCode(#[ExpectedValues(valuesFromClass: Responsibility::class)] int $code)
    {
        return Admin::where('role', Admin::ROLE_STAFF)->whereHas('responsibilities', function ($q) use ($code) {
            $q->where('code', $code);
        })->get();
    }*/

    public function getIndex(Request $request)
    {
        $query = Worker::orderByDesc('id');
        if (!empty($value = $request->get('post'))) {
            $query->where('post', $value);
        }
        return $query;
    }
}
