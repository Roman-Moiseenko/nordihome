<?php
declare(strict_types=1);

namespace Tests\Unit\Entity\User;

use App\Entity\User\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use DatabaseTransactions;
/*
    public function testChange(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        self::assertFalse($user->isAdmin());
        $user->changeRole(User::ROLE_ADMIN);
        self::assertTrue($user->isAdmin());
    }

    public function testAlready(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->expectExceptionMessage('Роль уже назначена.');
        $user->changeRole(User::ROLE_ADMIN);
    }
*/
}
