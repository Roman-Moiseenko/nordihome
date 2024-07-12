<?php


namespace App\Http\Controllers\_\Cabinet;


use App\Http\Controllers\Controller;
use App\Modules\User\Entity\User;
use App\Modules\User\Service\UserService;
use Illuminate\Http\Request;
use function response;
use function view;

class CabinetController extends Controller
{

    private UserService $service;

    public function __construct(UserService $service)
    {
        $this->middleware('auth:user');
        $this->service = $service;
    }

    public function view(User $user)
    {
        return $this->try_catch(function () {
            return view('cabinet.view');
        });
    }

    public function profile(User $user)
    {
        //
    }

    public function update(Request $request, User $user)
    {
        //
    }

    //AJAX

    public function fullname(User $user, Request $request)
    {
        return $this->try_catch_ajax(function () use ($user, $request) {
            $result = $this->service->setFullname($user, $request);
            $user->refresh();
            return response()->json($result);
        });
    }

    public function phone(User $user, Request $request)
    {
        return $this->try_catch_ajax(function () use ($user, $request) {
            $result = $this->service->setPhone($user, $request);
            $user->refresh();
            return response()->json($result);
        });
    }

    public function email(User $user, Request $request)
    {
        return $this->try_catch_ajax(function () use ($user, $request) {
            $result = $this->service->setEmail($user, $request);
            $user->refresh();
            return response()->json($result);
        });

    }

    public function password(User $user, Request $request)
    {
        return $this->try_catch_ajax(function () use ($user, $request) {
            $result = $this->service->setPassword($user, $request);
            $user->refresh();
            return response()->json($result);
        });
    }
}
