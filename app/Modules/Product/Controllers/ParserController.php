<?php
declare(strict_types=1);

namespace App\Modules\Product\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Repository\ParserRepository;
use App\Modules\Shop\Parser\ProductParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ParserController extends Controller
{
    private ParserRepository $repository;

    public function __construct(ParserRepository $repository)
    {
        $this->middleware(['auth:admin', 'can:product']);
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $categories = Category::defaultOrder()->withDepth()->get();
        $query = $this->repository->getFilter($request, $filters);
        $parsers = $this->pagination($query, $request, $pagination);

        return view('admin.product.parser.index', compact('parsers', 'pagination', 'categories', 'filters'));
    }


    public function show(ProductParser $productParser)
    {
        return view('admin.product.parser.show', compact('productParser'));
    }

    public function block(ProductParser $parser)
    {
        $parser->block();
        return redirect()->back();
    }

    public function unblock(ProductParser $parser)
    {
        $parser->unblock();
        return redirect()->back();
    }
}
