<?php

namespace App\Modules\Auth\Tests\Unit\Application\Actions\Password;

use App\Modules\Auth\Application\Actions\Password\RequestPasswordResetUseCase;
use App\Modules\Auth\Domain\Entities\UserEntity;
use App\Modules\Auth\Domain\Interfaces\PasswordResetTokenRepositoryInterface;
use App\Modules\Auth\Domain\Interfaces\UserRepositoryInterface;
use App\Modules\Auth\Domain\ValueObjects\Email;
use App\Modules\Auth\Domain\ValueObjects\HashedPassword;
use App\Modules\Auth\Domain\ValueObjects\ProfileType;
use App\Modules\Shared\Application\Interfaces\Mail\MailServiceInterface;
use Mockery;
use PHPUnit\Framework\TestCase;

class RequestPasswordResetUseCaseTest extends TestCase
{
    private UserRepositoryInterface $userRepo;
    private PasswordResetTokenRepositoryInterface $tokenRepo;
    private MailServiceInterface $mailService;
    private RequestPasswordResetUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepo = Mockery::mock(UserRepositoryInterface::class);
        $this->tokenRepo = Mockery::mock(PasswordResetTokenRepositoryInterface::class);
        $this->mailService = Mockery::mock(MailServiceInterface::class);
        $this->useCase = new RequestPasswordResetUseCase($this->userRepo, $this->tokenRepo, $this->mailService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_ignores_unknown_email(): void
    {
        $this->userRepo->shouldReceive('findByEmail')
            ->with(Mockery::on(fn(Email $e) => $e->value === 'unknown@example.com'))
            ->once()
            ->andReturn(null);
        $this->tokenRepo->shouldNotReceive('create');
        $this->mailService->shouldNotReceive('send');

        $this->useCase->execute('unknown@example.com');
        $this->assertTrue(true);
    }

    public function test_ignores_non_client_user(): void
    {
        $user = new UserEntity(new Email('staff@example.com'), HashedPassword::fromHash('hash'));
        $user->setProfile(ProfileType::STAFF, 3);

        $this->userRepo->shouldReceive('findByEmail')->once()->andReturn($user);
        $this->tokenRepo->shouldNotReceive('create');
        $this->mailService->shouldNotReceive('send');

        $this->useCase->execute('staff@example.com');
        $this->assertTrue(true);
    }
}
