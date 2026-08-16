<?php
declare(strict_types=1);

namespace App\Modules\Cabinet\Presentation\Http\Controllers;

use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Shop\Presentation\Http\Controllers\Web\ShopController;
use App\Modules\User\Entity\User;
use App\Modules\User\Repository\UserRepository;
use App\Modules\User\Service\WishService;
use Illuminate\Http\Request;
use function response;
use function view;

//MAINDO Сделать Избранное
class WishController extends ShopController
{
    private WishService $service;
    private UserRepository $repository;

    public function __construct(WishService $service, UserRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
      /*  $client = $this->getClient($request);
        $products = Product::whereHas('wishes', function ($query) use ($client) {
            $query->where('client_id', $client->id);
        })->get();*/
        return view('shop.cabinet.wish');
    }

    //Ajax
    public function toggle(Request $request, Product $product)
    {
        $client = $this->getClient($request);
        $result = $this->service->toggle($client->id, $product->id);

        return response()->json([
            'items' => [],
            'state' => $result,
        ]);
    }

    public function get(Request $request)
    {
        if (!auth()->check())
            return response()->json([
                'items' => [],
            ]);
        /** @var User $user */
        $client = $this->getClient($request);
        $products = $this->repository->getWish($client->id);
        return response()->json([
            'items' => $products,
        ]);
    }

    public function clear(Request $request)
    {
        /** @var User $user */
       // $this->service->clear($user->id);

        return response()->json(true);
    }

}
