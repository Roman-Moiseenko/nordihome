<?php

namespace App\Modules\Auth\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Application\Actions\Auth\LoginStaffUseCase;
use App\Modules\Auth\Application\Actions\Auth\LoginUserUseCase;
use App\Modules\Auth\Application\Actions\Auth\LogoutUserUseCase;
use App\Modules\Auth\Application\Actions\User\ConfirmEmailUseCase;
use App\Modules\Auth\Application\Actions\User\VerifyUserUseCase;
use App\Modules\Auth\Application\DTOs\LoginData;
use App\Modules\Auth\Application\Services\LoginOrRegisterUserService;
use App\Modules\Catalog\Infrastructure\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
class AuthController extends Controller
{
    public function __construct(
        private readonly LoginStaffUseCase          $loginStaffUser,
        private readonly LoginUserUseCase           $loginClientUseCase,
        private readonly LogoutUserUseCase          $logoutUser,
        private readonly LoginOrRegisterUserService $loginOrRegisterUserService,
        private readonly ConfirmEmailUseCase        $confirmEmailUseCase,
    )
    {
    }

    public function showLoginForm()
    {
        return Inertia::render('Auth/Staff/Login');
    }

    public function login(Request $request)
    {
        $dto = LoginData::validateAndCreate($request->all());
        $user = $this->loginStaffUser->execute($dto);
        $request->session()->regenerate();

        $fullName = $user->profileable?->fullName ?? $user->email;
        return redirect()->intended('/admin')
            ->with('success', 'Добро пожаловать ' . $fullName);
    }

    public function logout(Request $request)
    {
        $this->logoutUser->execute($request);
        return redirect('/admin/login');
    }

    public function logoutClient(Request $request)
    {
        $this->logoutUser->execute($request);
        return redirect('/');
    }

    public function loginClient(Request $request): JsonResponse
    {
        $dto = LoginData::validateAndCreate($request->all());
        //Выходим из другого логина, возможно это staff
        $this->logoutUser->execute($request);
        //Если клиент не верифицирован
        if (!is_null($dto->verify_token)) {
            try {
                //Верифицируемся
                $this->confirmEmailUseCase->execute($dto->verify_token);
                //Логинимся
                $result = $this->loginClientUseCase->execute($dto);
                return \response()->json($result ? 'login' : 'password');
            } catch (\InvalidArgumentException $e) {
                return \response()->json('token');
            }
        }
        $result = $this->loginOrRegisterUserService->execute($dto);
        return \response()->json($result);

    }

    public function verify(Request $request)
    {
        $this->confirmEmailUseCase->execute($request->input('token', ''));

        //TODO Возможно сделать страницу приветсвия
        return redirect('/')->with('success', 'Верификация прошла успешно');
    }


}
