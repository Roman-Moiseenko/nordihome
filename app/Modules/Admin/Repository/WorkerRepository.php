<?php
declare(strict_types=1);

namespace App\Modules\Admin\Repository;

use App\Modules\Admin\Entity\Admin;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Entity\Worker;
use Illuminate\Contracts\Support\Arrayable;
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

    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = Worker::orderByDesc('id');
        $filters = [];
        if (($post = $request->integer('post')) > 0) {
            $filters['post'] = $post;
            $query->where('post', $post);
        }
        if (count($filters) > 0) $filters['count'] = count($filters);

        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(Worker $worker) => $this->WorkerToArray($worker));
    }

    private function WorkerToArray(Worker $worker): array
    {
        return array_merge($worker->toArray(), [
            'storage_name' => $worker->storage->name,
        ]);
    }

    public function WorkerWithToArray(Worker $worker): array
    {
        return array_merge($this->WorkerToArray($worker), [

        ]);
    }
}
