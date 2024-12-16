<?php
declare(strict_types=1);

namespace App\Modules\Admin\Service;

use App\Modules\Admin\Entity\Worker;
use Illuminate\Http\Request;

class WorkerService
{

    public function register(Request $request): Worker
    {
        $worker = Worker::register(
            $request->string('surname')->trim()->value(),
            $request->string('firstname')->trim()->value(),
            $request->string('secondname')->trim()->value()
        );
        $this->save_fields($request, $worker);
        return $worker;
    }

    public function toggle(Worker $worker): void
    {
        if ($worker->isActive()) {
            $worker->draft();
        } else {
            $worker->activated();
        }
    }

    public function update(Request $request, Worker $worker): void
    {
        $worker->fullname->surname = $request->string('surname')->trim()->value();
        $worker->fullname->firstname = $request->string('firstname')->trim()->value();
        $worker->fullname->secondname = $request->string('secondname')->trim()->value();
        $worker->save();
        $this->save_fields($request, $worker);
    }

    public function save_fields(Request $request, Worker $worker): void
    {
        $worker->phone = phoneToDB($request->string('phone')->trim()->value());
        $worker->telegram_user_id = $request->input('telegram_user_id');
        $worker->storage_id = $request->input('storage_id');
        $worker->driver = $request->boolean('driver');
        $worker->loader = $request->boolean('loader');
        $worker->assemble = $request->boolean('assemble');
        $worker->logistic = $request->boolean('logistic');
        $worker->save();
    }

    public function destroy(Worker $worker): void
    {
        //TODO Сделать проверку на связанные данные
        $worker->delete();
    }
}
