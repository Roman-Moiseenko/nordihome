<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Discount;

use App\Modules\Discount\Entity\Promotion;
use App\Modules\Discount\Repository\PromotionRepository;
use App\Modules\Discount\Service\PromotionService;
use App\Modules\Product\Entity\Group;
use App\Modules\Product\Repository\GroupRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;

class PromotionController extends Controller
{
    private PromotionService $service;
    private PromotionRepository $repository;
    private mixed $pagination;
    private GroupRepository $groups;

    public function __construct(PromotionService $service, PromotionRepository $repository, GroupRepository $groups)
    {
        $this->service = $service;
        $this->repository = $repository;
        $this->pagination = Config::get('shop-config.p-list');
        $this->groups = $groups;
    }

    public function index(Request $request)
    {
        $pagination = $request['p'] ?? $this->pagination;
        $promotions = $this->repository->getIndex($pagination);
        if (isset($request['p'])) {
            $promotions->appends(['p' => $pagination]);
        }
        return view('admin.discount.promotion.index', compact('promotions', 'pagination'));
    }

    public function create()
    {
        return view('admin.discount.promotion.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ]);

        try {
            $promotion = $this->service->create($request);
            return redirect()->route('admin.discount.promotion.show', compact('promotion'));
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
            return back();
        }
    }

    public function show(Promotion $promotion)
    {

        $groups = $this->groups->getNotInPromotions($promotion); // Group::orderBy('name')->get(); //Перенести в Репозиторий, и исключать группы уже в действующих и будущих акциях
        return view('admin.discount.promotion.show', compact('promotion', 'groups'));
    }

    public function edit(Promotion $promotion)
    {
        return view('admin.discount.promotion.edit', compact('promotion'));
    }

    public function update(Request $request, Promotion $promotion)
    {
        try {
            $promotion = $this->service->update($request, $promotion);
            return redirect()->route('admin.discount.promotion.show', compact('promotion'));
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
            return back();
        }

    }

    public function add_group(Request $request, Promotion $promotion)
    {
        $request->validate([
            'group_id' => 'required|integer|gt:0',
            'discount' => 'required',
        ]);

        try {
            $this->service->add_group($request, $promotion);
        }  catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
            return back();
        }
        return redirect()->route('admin.discount.promotion.show', compact('promotion'));
    }

    public function del_group(Promotion $promotion, Group $group)
    {
        $this->service->del_group($group, $promotion);
        return redirect()->route('admin.discount.promotion.show', compact('promotion'));
    }

    public function set_group(Request $request, Promotion $promotion)
    {
        $this->service->set_group($request, $promotion);
        return redirect()->route('admin.discount.promotion.show', compact('promotion'));
    }


    public function destroy(Promotion $promotion)
    {
        try {
            $this->service->delete($promotion);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
            return back();
        }
        return redirect()->route('admin.discount.promotion.index');
    }

    //Команды
    public function draft(Promotion $promotion)
    {
        try {
            $this->service->draft($promotion);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        }
        return back();
    }

    public function published(Promotion $promotion)
    {
        try {
            $this->service->published($promotion);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        }
        return back();
    }

    public function stop(Promotion $promotion)
    {
        try {
            $this->service->stop($promotion);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        }
        return back();
    }

    public function start(Promotion $promotion)
    {
        try {
            $this->service->start($promotion);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        }
        return back();
    }
}
