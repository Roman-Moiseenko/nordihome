<?php
declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Modules\Product\Entity\Product;
use App\Modules\User\Entity\User;
use App\Modules\User\Entity\Wish;
use App\Modules\User\Service\WishService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class WishController extends Controller
{


    private WishService $service;

    public function __construct(WishService $service)
    {
        $this->middleware(['auth:user']);

        $this->service = $service;
    }

    public function index()
    {
        //return redirect()->route('home');
        try {
            $user_id = Auth::guard('user')->user()->id;
            $products = Product::whereHas('wishes', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })->get();
            return view('shop.cabinet.wish', $products);

        } catch (\Throwable $e) {
            flash($e->getMessage());
            return redirect()->route('home');
        }

    }

    //Ajax
    public function toggle(Request $request)
    {
        try {
            /** @var User $user */
            $user = Auth::guard('user')->user();
            $result = $this->service->toggle($user->id, (int)$request['product_id']);
            $products = array_map(function (Wish $wish) {
                return [
                    'image' => $wish->product->photo->getThumbUrl('thumb'),
                    'name' => $wish->product->name,
                    'url' => route('shop.product.view', $wish->product)
                ];
            }, $user->wishes()->getModels());
            return response()->json([
                'items' => $products,
                'state' => $result,
            ]);
        } catch (\Throwable $e) {
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
            return response()->json(['error' => $e->getMessage()]);
        }
    }

}
