<?php

namespace App\Http\Controllers;

use App\Events\ThrowableHasAppeared;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function try_catch($callback, $return = '', $level = 'warning')
    {
        DB::beginTransaction();
        try {
            $result = call_user_func($callback);
            DB::commit();
            return $result;
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
        DB::rollBack();
        return empty($return) ? redirect()->back() : redirect($return);
    }

    public function try_catch_ajax($callback)
    {
        DB::beginTransaction();
        try {
            $result = call_user_func($callback);
            DB::commit();
            return $result;
        } catch (\DomainException $e){
            DB::rollBack();
            return \response()->json(['error' => $e->getMessage()]);//Сообщение посетителю
        } catch (\Throwable $e) {
            DB::rollBack();
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
        } catch (\Throwable $e) {
            if (config('app.debug')) {
                flash($e->getMessage() . ' / ' . $e->getFile() . ' / ' . $e->getLine(), 'danger');
            } else {
                flash('Техническая ошибка! Информация направлена разработчику', 'danger');
                event(new ThrowableHasAppeared($e));
            }
        }
        DB::rollBack();
        return empty($return) ? redirect()->back() : redirect($return);
    }

    public function try_catch_ajax_admin($callback)
    {
        DB::beginTransaction();
        try {
            $result = call_user_func($callback);
            DB::commit();
            return $result;
        } catch (\DomainException $e) {
            DB::rollBack();
            return \response()->json(['error' => $e->getMessage()]);//Сообщение сотруднику
        } catch (\Throwable $e) {
            DB::rollBack();
            if (config('app.debug')) {
                //Вывод ошибки в консоль
                return \response()->json(['error' => [$e->getMessage(), $e->getFile(), $e->getLine()]]);
            } else {
                event(new ThrowableHasAppeared($e)); //Уведомляем админа
                return \response()->json(false); //Игнорируем вывод в консоль (error - undefined)
            }
        }
    }

    /**
     * Установка пагинации для запроса $query
     * @param $query - Построитель запросов
     * @param Request $request - Необходимо для параметра ->get('p')
     * @param $pagination - Кол-во элементов на страницу, передается во view
     * @return mixed - Возвращает пагинацию Модели paginate()
     */
    public function pagination($query, Request $request, &$pagination): mixed
    {
        if (!empty($pagination = $request->get('p'))) {
            $result = $query->paginate($pagination);
            $result->appends(['p' => $pagination]);
        } else {
            $pagination = Config::get('shop-config.p-list');
            $result = $query->paginate($pagination);
        }
        return $result;
    }
}
