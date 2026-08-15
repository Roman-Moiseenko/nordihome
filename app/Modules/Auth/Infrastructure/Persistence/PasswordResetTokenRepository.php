<?php

namespace App\Modules\Auth\Infrastructure\Persistence;

use App\Modules\Auth\Application\DTOs\Password\PasswordResetData;
use App\Modules\Auth\Application\Interfaces\PasswordResetTokenRepositoryInterface;
use App\Modules\Auth\Infrastructure\Models\PasswordReset;
use Carbon\Carbon;

use Illuminate\Support\Str;

class PasswordResetTokenRepository implements PasswordResetTokenRepositoryInterface
{
    public function create(string $email): string
    {
        // Удаляем старые токены для этого email (чтобы не плодить)
        $this->deleteByEmail($email);

        $token = Str::random(64); // или hash

        PasswordReset::create([
            'email' => $email,
            'token' => $token, // можно хранить hash вместо самого токена
            'created_at' => Carbon::now(),
        ]);

        return $token;
    }

    public function findValid(string $token):?PasswordResetData
    {
        $record = PasswordReset::where('token', $token)->first();
        if (!$record) return null;

        // Проверка срока действия (например, 60 минут)
        if ($record->created_at->addMinutes(60)->isPast()) {
            $this->delete($token);
            return null;
        }

        return new PasswordResetData(
            email: $record->email,
            token: $record->token,
            createdAt: $record->created_at->toImmutable(),
        );
    }

    public function delete(string $token): void
    {
        PasswordReset::where('token', $token)->delete();
    }

    public function deleteByEmail(string $email): void
    {
        PasswordReset::where('email', $email)->delete();
    }
}
