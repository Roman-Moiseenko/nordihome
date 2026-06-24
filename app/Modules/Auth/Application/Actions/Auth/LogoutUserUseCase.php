<?php

namespace App\Modules\Auth\Application\Actions\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class LogoutUserUseCase
{
    /**
     * Выход из системы. Работает как для web (сессии), так и для API (Sanctum).
     *
     * @param Request $request
     * @return void
     */
    public function execute(Request $request): void
    {
        // 1. Если есть bearer token — удаляем его (API/Sanctum)
        $token = $request->bearerToken();
        if ($token) {
            $accessToken = PersonalAccessToken::findToken($token);
            $accessToken?->delete();
        }

        // 2. Если есть аутентифицированный пользователь — выходим из сессии (web)
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
    }
}
