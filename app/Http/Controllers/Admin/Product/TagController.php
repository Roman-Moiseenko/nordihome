<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Product;

use App\Modules\Product\Entity\Tag;
use App\Modules\Product\Service\TagService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
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
        $pagination = $request['p'] ?? $this->pagination;
        $tags = Tag::orderBy('name')->paginate($this->pagination);
        if (isset($request['p'])) {
            $tags->appends(['p' => $pagination]);
        }
        return view('admin.product.tag.index', compact('tags', 'pagination'));
    }

    public function create(Request $request)
    {
        $this->service->create($request);
        return back();
    }

    public function rename(Request $request, Tag $tag)
    {
        $this->service->rename($request, $tag);
        return back();
    }

    public function destroy(Tag $tag)
    {
        $this->service->delete($tag);
        return back();
    }
}
