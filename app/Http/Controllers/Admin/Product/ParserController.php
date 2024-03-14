<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Product;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Shop\Parser\ProductParser;
use Illuminate\Http\Request;
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
        return $this->try_catch_admin(function () use($request) {
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
        });
    }


    public function show(ProductParser $productParser)
    {
        return $this->try_catch_admin(function () use($productParser) {
            return view('admin.product.parser.show', compact('productParser'));
        });
    }

    public function block(ProductParser $parser)
    {
        return $this->try_catch_admin(function () use($parser) {
            $parser->block();
            return redirect()->back();
        });
    }

    public function unblock(ProductParser $parser)
    {
        return $this->try_catch_admin(function () use($parser) {
            $parser->unblock();
            return redirect()->back();
        });
    }
}
