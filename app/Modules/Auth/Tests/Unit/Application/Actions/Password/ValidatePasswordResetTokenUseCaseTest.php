<?php

namespace App\Modules\Auth\Tests\Unit\Application\Actions\Password;

use App\Modules\Auth\Application\Actions\Password\ValidatePasswordResetTokenUseCase;
use App\Modules\Auth\Application\DTOs\Password\PasswordResetData;
use App\Modules\Auth\Domain\Interfaces\PasswordResetTokenRepositoryInterface;
use DateTimeImmutable;
use Mockery;
use PHPUnit\Framework\TestCase;

class ValidatePasswordResetTokenUseCaseTest extends TestCase
{
    private PasswordResetTokenRepositoryInterface $tokenRepo;
    private ValidatePasswordResetTokenUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tokenRepo = Mockery::mock(PasswordResetTokenRepositoryInterface::class);
        $this->useCase = new ValidatePasswordResetTokenUseCase($this->tokenRepo);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_returns_true_for_valid_token(): void
    {
        $reset = new PasswordResetData('a@example.com', 'token', new DateTimeImmutable());
        $this->tokenRepo->shouldReceive('findValid')->with('token')->once()->andReturn($reset);

        $this->assertTrue($this->useCase->execute('token'));
    }

    public function test_returns_false_for_invalid_token(): void
    {
        $this->tokenRepo->shouldReceive('findValid')->with('bad')->once()->andReturn(null);

        $this->assertFalse($this->useCase->execute('bad'));
    }
}
