<?php

namespace App\Modules\Auth\Domain\Interfaces;

use App\Modules\Auth\Application\DTOs\Password\PasswordResetData;

interface PasswordResetTokenRepositoryInterface
{
    /**
     * Создать токен для указанного email.
     * Возвращает сгенерированный токен (строку).
     */
    public function create(string $email): string;

    /**
     * Найти валидный токен (не просроченный).
     * Если токен не найден или просрочен — вернуть null.
     */
    public function findValid(string $token): ?PasswordResetData;

    /**
     * Удалить токен (после использования или по запросу).
     */
    public function delete(string $token): void;

    /**
     * Удалить все токены для указанного email (например, при повторном запросе сброса).
     */
    public function deleteByEmail(string $email): void;
}
