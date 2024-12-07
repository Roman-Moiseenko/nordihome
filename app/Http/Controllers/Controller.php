<?php

namespace App\Http\Controllers;

use App\Events\ThrowableHasAppeared;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\Deprecated;
use Illuminate\Http\Request;

class Controller  extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

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

