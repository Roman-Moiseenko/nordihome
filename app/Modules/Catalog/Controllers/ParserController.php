<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Repository\ParserRepository;
use App\Modules\Shop\Parser\ProductParser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ParserController extends Controller
{
    private ParserRepository $repository;

    public function __construct(ParserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request): Response
    {
        $parsers = $this->repository->getFilter($request, $filters);
        return Inertia::render('Catalog/Parser/Index', [
            'parsers' => $parsers,
            'filters' => $filters,
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
