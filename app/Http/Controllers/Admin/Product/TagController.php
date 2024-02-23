<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Product;

use App\Events\ThrowableHasAppeared;
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
        try {
            $pagination = $request['p'] ?? $this->pagination;
            $tags = Tag::orderBy('name')->paginate($this->pagination);
            if (isset($request['p'])) {
                $tags->appends(['p' => $pagination]);
            }
            return view('admin.product.tag.index', compact('tags', 'pagination'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function create(Request $request)
    {
        try {
            $this->service->create($request['name']);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function rename(Request $request, Tag $tag)
    {
        try {
            $this->service->rename($request, $tag);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function destroy(Tag $tag)
    {
        try {
            $this->service->delete($tag);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }
}
