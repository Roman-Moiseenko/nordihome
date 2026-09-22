<?php
declare(strict_types=1);

namespace App\Modules\Shop\Presentation\Http\Controllers\Web;

use App\Modules\Shop\Application\Queries\Page\PageViewQuery;
use Illuminate\Contracts\View\View;

class PageAbstractController extends \App\Modules\Shop\Presentation\Http\Controllers\Web\ShopAbstractController
{

    public function __construct(
        private readonly PageViewQuery $pageViewQuery
    )
    {
    }

    public function home(): View
    {
        $pageData = $this->pageViewQuery->execute('home');
        if (!is_null($pageData)) return view($pageData->template, ['pageData' => $pageData]);

        return view('shop.home');
    }

    public function view($slug): View
    {
        $pageData = $this->pageViewQuery->execute($slug);
        if (is_null($pageData)) abort(404, 'Страница не найдена');

        return view($pageData->template, ['pageData' => $pageData]);
    }

}
