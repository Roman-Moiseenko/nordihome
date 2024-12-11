<?php
declare(strict_types=1);

namespace App\Modules\Admin\Service;

use App\Modules\Admin\Entity\Worker;
use Illuminate\Http\Request;

class WorkerService
{

    public function register(Request $request): Worker
    {
        if ((int)$request['post'] == 0) throw new \DomainException('Не выбрана специализация');
        $worker = Worker::register(
            $request->string('surname')->trim()->value(),
            $request->string('firstname')->trim()->value(),
            $request->string('secondname')->trim()->value(),
            $request->integer('post'),
        );
        $worker->setPhone($request->string('phone')->trim()->value());
        if (!is_null($request['telegram_user_id'])) $worker->setTelegram($request->integer('telegram_user_id'));
        if ($request->integer('storage_id') != 0) $worker->setStorage($request->integer('storage_id'));
        return $worker;
    }

    public function toggle(Worker $worker)
    {
        if ($worker->isActive()) {
            $worker->draft();
        } else {
            $worker->activated();
        }
    }

    public function update(Request $request, Worker $worker): Worker
    {
        $worker->fullname->surname = $request->string('surname')->trim()->value();
        $worker->fullname->firstname = $request->string('firstname')->trim()->value();
        $worker->fullname->secondname = $request->string('secondname')->trim()->value();
        $worker->save();

        $worker->update([
            'post' => $request->integer('post'),
        ]);
        $worker->setPhone($request->string('phone')->trim()->value());

        if (!is_null($request['telegram_user_id'])) $worker->setTelegram($request->integer('telegram_user_id'));
        if ($request->integer('storage_id') != 0) $worker->setStorage($request->integer('storage_id'));
        $worker->refresh();
        return $worker;
    }

    public function destroy(Worker $worker)
    {
        //TODO Сделать проверку на связанные данные
        $worker->delete();
    }
}
