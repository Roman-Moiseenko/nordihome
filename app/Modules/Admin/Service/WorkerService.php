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
            $request['surname'],
            $request['firstname'],
            $request['secondname'] ?? '',
            (int)$request['post'],
            $request['phone'] ?? '',
        );
        if (!is_null($request['telegram_user_id'])) $worker->setTelegram((int)$request['telegram_user_id']);
        if (!is_null($request['storage_id']) && (int)$request['storage_id'] != 0) $worker->setStorage((int)$request['storage_id']);
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

        $worker->fullname->surname = $request['surname'];
        $worker->fullname->firstname = $request['firstname'];
        $worker->fullname->secondname = $request['secondname'] ?? '';
        $worker->save();

        $worker->update([
            'post' => (int)$request['post'],
            'phone' => $request['phone'] ?? '',
        ]);
        if (!is_null($request['telegram_user_id'])) $worker->setTelegram((int)$request['telegram_user_id']);
        if (!is_null($request['storage_id']) && (int)$request['storage_id'] != 0) $worker->setStorage((int)$request['storage_id']);
        $worker->refresh();
        return $worker;
    }

    public function destroy(Worker $worker)
    {
        //TODO Сделать проверку на связанные данные
        $worker->delete();
    }
}
