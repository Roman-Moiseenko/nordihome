<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Product;

use App\Events\ThrowableHasAppeared;
use App\Modules\Product\Entity\Category;
use App\Modules\Shop\Parser\ProductParser;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;

class ParserController extends Controller
{

    private mixed $pagination;

    public function __construct()
    {
        $this->middleware(['auth:admin', 'can:commodity']);
        $this->pagination = Config::get('shop-config.p-list');
    }

    public function index(Request $request)
    {
        try {
            $published = $request['published'] ?? 'all';

            $query = ProductParser::orderBy('created_at');
            if ($published == 'active') $query->whereHas('product', function ($q) {
                $q->where('published', '=', true);
            });
            if ($published == 'draft') $query->whereHas('product', function ($q) {
                $q->where('published', '=', false);
            });

            //ПАГИНАЦИЯ
            if (!empty($pagination = $request->get('p'))) {
                $parsers = $query->paginate($pagination);
                $parsers->appends(['p' => $pagination]);
            } else {
                $parsers = $query->paginate($this->pagination);
            }
            return view('admin.product.parser.index', compact('parsers', 'pagination', 'published'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }


    public function show(ProductParser $productParser)
    {
        try {
            return view('admin.product.parser.show', compact('productParser'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function block(ProductParser $parser)
    {
        try {
            $parser->block();
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function unblock(ProductParser $parser)
    {
        try {
            $parser->unblock();
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }
}
