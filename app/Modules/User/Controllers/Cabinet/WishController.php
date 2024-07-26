<?php
declare(strict_types=1);

namespace App\Modules\User\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Product;
use App\Modules\User\Entity\User;
use App\Modules\User\Repository\UserRepository;
use App\Modules\User\Service\WishService;
use Illuminate\Support\Facades\Auth;
use function response;
use function view;

class WishController extends Controller
{
    private WishService $service;
    private UserRepository $repository;

    public function __construct(WishService $service, UserRepository $repository)
    {
        $this->middleware(['auth:user'])->except('get');
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index()
    {
        return $this->try_catch(function () {
            $user_id = Auth::guard('user')->user()->id;
            $products = Product::whereHas('wishes', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })->get();
            return view('cabinet.wish', compact('products'));
        });
    }

    //Ajax
    public function toggle(Product $product)
    {
        return $this->try_catch_ajax(function () use ($product) {
            /** @var User $user */
            $user = Auth::guard('user')->user();
            $result = $this->service->toggle($user->id, $product->id);
            $products = $this->repository->getWish($user);

            return response()->json([
                'items' => $products,
                'state' => $result,
            ]);
        });
    }

    public function get()
    {
        return $this->try_catch_ajax(function () {
            if (!Auth::guard('user')->check())
                return response()->json([
                    'items' => [],
                ]);
            /** @var User $user */
            $user = Auth::guard('user')->user();
            $products = $this->repository->getWish($user);
            return response()->json([
                'items' => $products,
            ]);
        });
    }

    public function clear()
    {
        return $this->try_catch_ajax(function () {
            /** @var User $user */
            $user = Auth::guard('user')->user();
            $this->service->clear($user->id);

            return response()->json(true);
        });
    }

}
