<?php


namespace App\Http\Controllers\User;


use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\User\Entity\User;
use Illuminate\Http\Request;

class CabinetController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:user');
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
}
