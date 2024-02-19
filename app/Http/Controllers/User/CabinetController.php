<?php


namespace App\Http\Controllers\User;


use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\User\Entity\User;
use App\Modules\User\Service\UserService;
use Illuminate\Http\Request;

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
        try {
            return view('cabinet.view');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Непредвиденная ошибка. Мы уже работаем над ее исправлением', 'info');
        }
        return redirect()->back();
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
        try {
            $result = $this->service->setFullname($user, $request);
            $user->refresh();
            return response()->json($result);
        } catch (\Throwable $e) {
            return response()->json([$e->getMessage(),$e->getFile(), $e->getLine()]);
        }
    }

    public function phone(User $user, Request $request)
    {
        try {
            $result = $this->service->setPhone($user, $request);
            $user->refresh();
            return response()->json($result);
        } catch (\Throwable $e) {
            return response()->json([$e->getMessage(),$e->getFile(), $e->getLine()]);
        }
    }

    public function email(User $user, Request $request)
    {
        try {
            $result = $this->service->setEmail($user, $request);
            $user->refresh();
            return response()->json($result);
        } catch (\Throwable $e) {
            return response()->json([$e->getMessage(),$e->getFile(), $e->getLine()]);
        }
    }

    public function password(User $user, Request $request)
    {
        try {
            $result = $this->service->setPassword($user, $request);
            $user->refresh();
            return response()->json($result);
        } catch (\Throwable $e) {
            return response()->json([$e->getMessage(),$e->getFile(), $e->getLine()]);
        }
    }
}
