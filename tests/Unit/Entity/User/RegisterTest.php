<?php
declare(strict_types=1);

namespace Tests\Unit\Entity\User;



use App\Modules\User\Entity\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use DatabaseTransactions;

    public function testRequest(): void
    {
        $user = User::register(
            $email = 'email',
            $phone = 'phone-number',
            $password = 'password'
        );

        self::assertNotEmpty($user);

        self::assertEquals($phone, $user->phone);
        self::assertEquals($email, $user->email);
        self::assertNotEmpty($user->password);
        self::assertNotEquals($password, $user->password);

        self::assertTrue($user->isWait());
        self::assertFalse($user->isActive());
    }

    public function testVerify(): void
    {
        $user = User::register( 'email', '880000000', 'password');

        $user->verify();

        self::assertFalse($user->isWait());
        self::assertTrue($user->isActive());
    }

    public function testAlreadyVerified(): void
    {
        $user = User::register('email', '880000000', 'password');
        $user->verify();

        $this->expectExceptionMessage('User is already verified.');
        $user->verify();
    }
    public function testFullName(): void
    {
        $user = User::new( 'email', 'phone');
        $user->save();

    }
}
