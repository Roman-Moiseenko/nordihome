<?php

namespace App\Modules\Auth\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Application\Actions\Auth\LoginUserUseCase;
use App\Modules\Auth\Application\Actions\Auth\LogoutUserUseCase;
use App\Modules\Auth\Application\Actions\Auth\ResetPasswordUseCase;
use App\Modules\Auth\Application\Actions\Auth\SendPasswordResetLinkUseCase;
use App\Modules\Auth\Application\Actions\User\GetUserProfileUseCase;
use App\Modules\Auth\Application\DTOs\LoginData;
use App\Modules\Auth\Domain\Exceptions\InvalidCredentialsException;
use App\Modules\Auth\Infrastructure\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(
        // private readonly RegisterUserUseCase          $registerUser,
        private readonly LoginUserUseCase             $loginUser,
        private readonly LogoutUserUseCase            $logoutUser,
        private readonly SendPasswordResetLinkUseCase $sendResetLink,
        private readonly ResetPasswordUseCase         $resetPassword,
        private readonly GetUserProfileUseCase        $getUserProfileUseCase
        //  private readonly AssignRoleToUserUseCase      $assignRoleUser,
    )
    {
    }

    public function showLoginForm()
    {
        return Inertia::render('Admin/Auth/Login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $name = $request->input('name');
        $domain = Config::get('auth.staff_domain', '@nordihome.ru');
        $email = $name . $domain;

        if (Auth::attempt(
            ['email' => $email, 'password' => $request->input('password')],
            $request->boolean('remember')
        )) {
            /** @var User $user */
            $user = Auth::user();

            // Проверяем, что пользователь — сотрудник (по роли Spatie)
            if (!$user->hasAnyRole(['admin', 'staff'])) {
                Auth::logout();
                throw new \DomainException('Доступ разрешён только сотрудникам');
            }

            if ($user->banned_at) {
                Auth::logout();
                throw new \DomainException('Ваш аккаунт заблокирован');
            }

            $request->session()->regenerate();

            $fullName = $user->profileable?->fullName ?? $user->email;

            return redirect()->intended('/admin')
                ->with('success', 'Добро пожаловать ' . $fullName);
        }

        throw new \DomainException('Неверный логин или пароль');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }


}
