<?php


namespace App\Http\Controllers\User;


use App\Http\Controllers\Controller;
use App\Modules\User\Entity\User;
use Illuminate\Http\Request;

class CabinetController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:user');
    }

    public function cabinet(User $user)
    {
        //
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
