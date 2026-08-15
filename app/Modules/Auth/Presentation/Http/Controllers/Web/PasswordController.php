<?php

namespace App\Modules\Auth\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Application\Actions\Password\RequestPasswordResetUseCase;
use App\Modules\Auth\Application\Actions\Password\ResetPasswordUseCase;
use App\Modules\Auth\Application\Actions\Password\ValidatePasswordResetTokenUseCase;
use Illuminate\Http\Request;

class PasswordController extends Controller
{
    public function __construct(
        private readonly RequestPasswordResetUseCase $requestPasswordResetUseCase,
        private readonly ResetPasswordUseCase        $resetPasswordUseCase,
        private readonly ValidatePasswordResetTokenUseCase $validatePasswordResetTokenUseCase,
    )
    {

    }

    public function showRequestForm()
    {
        return view('user.password.request');
    }

    public function sendResetEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $this->requestPasswordResetUseCase->execute($request->email);
        return redirect()
            ->route('shop.home')
            ->with('success', 'Ссылка для сброса отправлена на почту');
    }

    public function showResetForm(string $token)
    {
        if (!$this->validatePasswordResetTokenUseCase->execute($token)) {
            // Вернём пользователя на страницу запроса с ошибкой
            return redirect()->route('shop.home')
                ->with('danger', 'Ссылка для сброса пароля недействительна или устарела.');
        }
        return view('user.password.reset', ['token' => $token]);
    }

    public function updatePassword(Request $request, string $token)
    {

        $request->validate([
            'password' => 'required|confirmed|min:6',
        ]);

        $this->resetPasswordUseCase->execute($request->token, $request->password);

        return redirect()->route('shop.home')->with('success', 'Пароль успешно изменён');
    }
}
