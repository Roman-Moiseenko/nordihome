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

    public function __construct(TagService $service)
    {
        $this->middleware(['auth:admin', 'can:product']);
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $query = Tag::orderBy('name');
            $tags = $this->pagination($query, $request, $pagination);
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
