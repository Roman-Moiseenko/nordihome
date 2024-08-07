<?php
declare(strict_types=1);

namespace App\Modules\Shop\Controllers;

use App\Modules\Page\Entity\Page;
use App\Modules\Shop\ShopRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PageController extends Controller
{

    private ShopRepository $repository;

    public function __construct(ShopRepository $repository)
    {
        $this->repository = $repository;
    }

    public function view($slug)
    {
        try {
            $page = Page::where('slug', $slug)->where('published', true)->firstOrFail();
            return $page->view();
        } catch (\Throwable $e) {
            abort(404, 'Страница не найдена');
        }
    }

    public function map_data(Request $request)
    {
            $map = $this->repository->getMapData($request);
            return response()->json($map);
    }

    public function email(Request $request)
    {
        abort(404);
        //TODO Обратная связь
        // Необходимо сохранение писем в базе и передача их на Исполнение
        //Mail::to($request['email'])->queue(new FeedBack($request['email'], $request['phone'], $request['message']));
        //return redirect()->back();
    }
}
