<?php
declare(strict_types=1);

namespace App\Modules\Product\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Repository\CategoryRepository;
use App\Modules\Product\Repository\ParserRepository;
use App\Modules\Shop\Parser\ParserService;
use App\Modules\Shop\Parser\ProductParser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Inertia\Inertia;
use Inertia\Response;

class ParserController extends Controller
{
    private ParserRepository $repository;
    private CategoryRepository $categories;

    public function __construct(ParserRepository $repository, CategoryRepository $categories)
    {
        $this->middleware(['auth:admin', 'can:product']);
        $this->repository = $repository;
        $this->categories = $categories;
    }

    public function index(Request $request): Response
    {
        $categories = $this->categories->forFilters();
        $parsers = $this->repository->getFilter($request, $filters);
        return Inertia::render('Product/Parser/Index', [
            'parsers' => $parsers,
            'filters' => $filters,
            'categories' => $categories,
        ]);
    }

    public function block(ProductParser $parser): RedirectResponse
    {
        if ($parser->isBlock()) {
            $message = 'Товар доступен для заказа';
            $parser->unblock();
        } else {
            $parser->block();
            $message = 'Товар заблокирован для заказа';
        }
        return redirect()->back()->with('success', $message);
    }

    public function fragile(ProductParser $parser): RedirectResponse
    {
        $parser->toggleFragile();
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function sanctioned(ProductParser $parser): RedirectResponse
    {
        $parser->toggleSanctioned();
        return redirect()->back()->with('success', 'Сохранено');
    }
}
