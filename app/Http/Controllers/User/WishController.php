<?php
declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Events\ThrowableHasAppeared;
use App\Modules\Product\Entity\Product;
use App\Modules\User\Entity\User;
use App\Modules\User\Service\WishService;
use App\Modules\User\UserRepository;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class WishController extends Controller
{
    private WishService $service;
    private UserRepository $repository;

    public function __construct(WishService $service, UserRepository $repository)
    {
        $this->middleware(['auth:user']);
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index()
    {
        try {
            $user_id = Auth::guard('user')->user()->id;
            $products = Product::whereHas('wishes', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })->get();

            return view('cabinet.wish', compact('products'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Непредвиденная ошибка. Мы уже работаем над ее исправлением', 'info');
        }
        return redirect()->back();
    }

    //Ajax
    public function toggle(Product $product)
    {
        try {
            /** @var User $user */
            $user = Auth::guard('user')->user();
            $result = $this->service->toggle($user->id, $product->id);
            $products = $this->repository->getWish($user);

            return response()->json([
                'items' => $products,
                'state' => $result,
            ]);
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function get()
    {
        try {
            /** @var User $user */
            $user = Auth::guard('user')->user();
            $products = $this->repository->getWish($user);

            return response()->json([
                'items' => $products,
            ]);
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function clear()
    {
        try {
            /** @var User $user */
            $user = Auth::guard('user')->user();
            $this->service->clear($user->id);

            return response()->json(true);
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            return response()->json(['error' => $e->getMessage()]);
        }
    }

}
