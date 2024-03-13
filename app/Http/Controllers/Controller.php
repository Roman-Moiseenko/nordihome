<?php

namespace App\Http\Controllers;

use App\Events\ThrowableHasAppeared;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function try_catch($callback, $return = '', $level = 'warning')
    {
        try {
            return call_user_func($callback);
        } catch (\DomainException $e) {
            flash($e->getMessage(), $level);
        } catch (\Throwable $e) {
            if (config('app.debug')) {
                flash($e->getMessage() . ' / ' . $e->getFile() . ' / ' . $e->getLine(), 'danger');
            } else {
                flash('Непредвиденная ошибка. Мы уже работаем над ее исправлением', 'danger');
                event(new ThrowableHasAppeared($e));
            }
        }
        return empty($return) ? redirect()->back() : redirect($return);
    }

    public function try_catch_ajax($callback)
    {
        try {
            return call_user_func($callback);
        } catch (\DomainException $e){
            return \response()->json(['error' => $e->getMessage()]);//Сообщение посетителю
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

    public function try_catch_admin($callback, $return = '', $level = 'danger')
    {
        DB::beginTransaction();
        try {
            $result = call_user_func($callback);
            DB::commit();
            return $result;
        } catch (\DomainException $e) {
            flash($e->getMessage(), $level);
            DB::rollBack();
            return empty($return) ? redirect()->back() : redirect($return);
        } catch (\Throwable $e) {
            if (config('app.debug')) {
                flash($e->getMessage() . ' / ' . $e->getFile() . ' / ' . $e->getLine(), 'danger');
            } else {
                flash('Техническая ошибка! Информация направлена разработчику', 'danger');
                event(new ThrowableHasAppeared($e));
            }
            DB::rollBack();
            return empty($return) ? redirect()->back() : redirect($return);
        }
    }

    public function try_catch_ajax_admin($callback)
    {
        DB::beginTransaction();
        try {
            $result = call_user_func($callback);
            DB::commit();
            return $result;
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
