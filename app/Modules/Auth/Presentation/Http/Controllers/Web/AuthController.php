<?php

namespace App\Modules\Auth\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Application\Actions\Auth\LoginStaffUseCase;
use App\Modules\Auth\Application\Actions\Auth\LogoutUserUseCase;
use App\Modules\Auth\Application\DTOs\LoginData;
use Illuminate\Http\Request;
use Inertia\Inertia;
class AuthController extends Controller
{
    public function __construct(
        private readonly LoginStaffUseCase $loginUser,
        private readonly LogoutUserUseCase $logoutUser,
    )
    {
    }

    public function showLoginForm()
    {
        return Inertia::render('Auth/Staff/Login');
    }

    public function login(Request $request)
    {
        $dto = LoginData::validateAndCreate($request);
        $fullName = $this->loginUser->execute($dto);
        $request->session()->regenerate();
        return redirect()->intended('/admin')
            ->with('success', 'Добро пожаловать ' . $fullName);
    }

    public function logout(Request $request)
    {
        $this->logoutUser->execute($request);
        return redirect('/admin/login');
    }
}
