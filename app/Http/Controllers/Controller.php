<?php

namespace App\Http\Controllers;

use App\Events\ThrowableHasAppeared;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function try_catch($callback, $return = '')
    {
        try {
            return call_user_func($callback);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            flash('Непредвиденная ошибка. Мы уже работаем над ее исправлением', 'info');
            event(new ThrowableHasAppeared($e));
        }
        return empty($return) ? redirect()->back() : redirect($return);
    }

    public function try_catch_ajax($callback)
    {
        try {
            return call_user_func($callback);
        } catch (\DomainException $e){
            //Сообщение посетителю
            return \response()->json(['error' => $e->getMessage()]);
        } catch (\Throwable $e) {
            if (config('app.debug')) {
                //Вывод ошибки в консоль
                return \response()->json(['error' => [$e->getMessage(), $e->getFile(), $e->getLine()]]);
            } else {
                event(new ThrowableHasAppeared($e)); //Уведомляем админа
                return \response()->json(false); //Игнорируем вывод в консоль (error - undefined)
            }
        }
    }
}
