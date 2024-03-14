<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Product;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Tag;
use App\Modules\Product\Service\TagService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class TagController extends Controller
{
    private TagService $service;
    private mixed $pagination;

    public function __construct(TagService $service)
    {
        $this->service = $service;
        $this->pagination = Config::get('shop-config.p-list');
    }

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $pagination = $request['p'] ?? $this->pagination;
            $tags = Tag::orderBy('name')->paginate($this->pagination);
            if (isset($request['p'])) {
                $tags->appends(['p' => $pagination]);
            }
            return view('admin.product.tag.index', compact('tags', 'pagination'));
        });
    }

    public function create(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $this->service->create($request['name']);
            return redirect()->back();
        });
    }

    public function rename(Request $request, Tag $tag)
    {
        return $this->try_catch_admin(function () use($request, $tag) {
            $this->service->rename($request, $tag);
            return redirect()->back();
        });
    }

    public function destroy(Tag $tag)
    {
        return $this->try_catch_admin(function () use($tag) {
            $this->service->delete($tag);
            return redirect()->back();
        });
    }
}
