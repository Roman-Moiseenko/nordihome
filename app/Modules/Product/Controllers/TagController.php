<?php
declare(strict_types=1);

namespace App\Modules\Product\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Tag;
use App\Modules\Product\Repository\TagRepository;
use App\Modules\Product\Service\TagService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Inertia\Inertia;

class TagController extends Controller
{
    private TagService $service;
    private TagRepository $repository;

    public function __construct(TagService $service, TagRepository $repository)
    {
        $this->middleware(['auth:admin', 'can:product']);
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request): \Inertia\Response
    {
        $tags = $this->repository->getIndex($request, $filters);
        return Inertia::render('Product/Tag/Index', [
            'tags' => $tags,
            'filters' => $filters,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $this->service->create($request['name']);
            return redirect()->back()->with('success', 'Метка создана');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function rename(Request $request, Tag $tag): RedirectResponse
    {
        try {
            $this->service->rename($request, $tag);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        try {
            $this->service->delete($tag);
            return redirect()->back()->with('success', 'Метка удалена');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
