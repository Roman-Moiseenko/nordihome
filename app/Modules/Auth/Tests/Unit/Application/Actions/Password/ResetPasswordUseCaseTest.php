<?php

namespace App\Modules\Auth\Tests\Unit\Application\Actions\Password;

use App\Modules\Auth\Application\Actions\Password\ResetPasswordUseCase;
use App\Modules\Auth\Application\DTOs\Password\PasswordResetData;
use App\Modules\Auth\Application\Interfaces\PasswordResetTokenRepositoryInterface;
use App\Modules\Auth\Application\Interfaces\UserRepositoryInterface;
use App\Modules\Auth\Domain\Entities\UserEntity;
use App\Modules\Auth\Domain\Services\PasswordHasherInterface;
use App\Modules\Auth\Domain\ValueObjects\Email;
use App\Modules\Auth\Domain\ValueObjects\HashedPassword;
use App\Modules\Auth\Domain\ValueObjects\ProfileType;
use DateTimeImmutable;
use InvalidArgumentException;
use Mockery;
use PHPUnit\Framework\TestCase;

class ResetPasswordUseCaseTest extends TestCase
{
    private PasswordResetTokenRepositoryInterface $tokenRepo;
    private UserRepositoryInterface $userRepo;
    private PasswordHasherInterface $hasher;
    private ResetPasswordUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tokenRepo = Mockery::mock(PasswordResetTokenRepositoryInterface::class);
        $this->userRepo = Mockery::mock(UserRepositoryInterface::class);
        $this->hasher = Mockery::mock(PasswordHasherInterface::class);
        $this->hasher->shouldReceive('make')->andReturnUsing(fn($p) => 'hashed_' . $p);
        $this->useCase = new ResetPasswordUseCase($this->tokenRepo, $this->userRepo, $this->hasher);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function createClientUser(): UserEntity
    {
        $user = new UserEntity(new Email('client@example.com'), HashedPassword::fromHash('old_hash'));
        $user->id = 5;
        $user->setProfile(ProfileType::CLIENT, 10);
        return $user;
    }

    public function test_resets_password(): void
    {
        $reset = new PasswordResetData('client@example.com', 'token', new DateTimeImmutable());
        $user = $this->createClientUser();

        $this->tokenRepo->shouldReceive('findValid')->with('token')->once()->andReturn($reset);
        $this->userRepo->shouldReceive('findByEmail')
            ->with(Mockery::on(fn(Email $e) => $e->value === 'client@example.com'))
            ->once()
            ->andReturn($user);
        $this->userRepo->shouldReceive('save')->once()->with($user)->andReturn($user);
        $this->tokenRepo->shouldReceive('delete')->once()->with('token');

        $this->useCase->execute('token', 'new_password');

        $this->assertSame('hashed_new_password', $user->getPasswordHash());
    }

    public function test_throws_on_invalid_token(): void
    {
        $this->tokenRepo->shouldReceive('findValid')->with('bad')->once()->andReturn(null);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Недействительный токен');
        $this->useCase->execute('bad', 'new_password');
    }

    public function test_throws_when_user_not_found(): void
    {
        $reset = new PasswordResetData('missing@example.com', 'token', new DateTimeImmutable());
        $this->tokenRepo->shouldReceive('findValid')->with('token')->once()->andReturn($reset);
        $this->userRepo->shouldReceive('findByEmail')->once()->andReturn(null);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Клиент не найден');
        $this->useCase->execute('token', 'new_password');
    }

    public function test_ignores_non_client_user(): void
    {
        $reset = new PasswordResetData('staff@example.com', 'token', new DateTimeImmutable());
        $user = new UserEntity(new Email('staff@example.com'), HashedPassword::fromHash('hash'));
        $user->setProfile(ProfileType::STAFF, 3);

        $this->tokenRepo->shouldReceive('findValid')->with('token')->once()->andReturn($reset);
        $this->userRepo->shouldReceive('findByEmail')->once()->andReturn($user);
        $this->userRepo->shouldNotReceive('save');
        $this->tokenRepo->shouldNotReceive('delete');

        $this->useCase->execute('token', 'new_password');
        $this->assertTrue(true);
    }
}
