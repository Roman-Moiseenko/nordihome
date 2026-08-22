<?php

namespace App\Modules\Auth\Tests\Unit\Application\Actions\User;

use App\Modules\Auth\Application\Actions\Client\ConsentClientUseCase;
use App\Modules\Auth\Application\Actions\User\ConfirmEmailUseCase;
use App\Modules\Auth\Application\Interfaces\ClientRepositoryInterface;
use App\Modules\Auth\Application\Interfaces\UserRepositoryInterface;
use App\Modules\Auth\Domain\Entities\ClientEntity;
use App\Modules\Auth\Domain\Entities\UserEntity;
use App\Modules\Auth\Domain\ValueObjects\Email;
use App\Modules\Auth\Domain\ValueObjects\FullName;
use App\Modules\Auth\Domain\ValueObjects\HashedPassword;
use App\Modules\Auth\Domain\ValueObjects\ProfileType;
use InvalidArgumentException;
use Mockery;
use PHPUnit\Framework\TestCase;

class ConfirmEmailUseCaseTest extends TestCase
{
    private UserRepositoryInterface $userRepo;
    private ClientRepositoryInterface $clientRepo;
    private ConfirmEmailUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepo = Mockery::mock(UserRepositoryInterface::class);
        $this->clientRepo = Mockery::mock(ClientRepositoryInterface::class);
        $consent = new ConsentClientUseCase($this->clientRepo);
        $this->useCase = new ConfirmEmailUseCase($this->userRepo, $consent);

        \Carbon\Carbon::setTestNow('2026-01-01 12:00:00');
    }

    protected function tearDown(): void
    {
        \Carbon\Carbon::setTestNow(null);
        Mockery::close();
        parent::tearDown();
    }

    private function createUser(string $email = 'old@example.com'): UserEntity
    {
        $user = new UserEntity(new Email($email), HashedPassword::fromHash('hash'));
        $user->id = 1;
        $user->setProfile(ProfileType::CLIENT, 10);
        return $user;
    }

    private function createClient(int $id = 10): ClientEntity
    {
        $client = new ClientEntity(
            new FullName('Иванов Иван'),
            new Email('client@example.com'),
        );
        $client->id = $id;
        return $client;
    }

    private function expectConsent(int $clientId = 10): void
    {
        $client = $this->createClient($clientId);
        $this->clientRepo->shouldReceive('findById')->with($clientId)->once()->andReturn($client);
        $this->clientRepo->shouldReceive('save')->once()->with($client)->andReturn($client);
    }

    public function test_confirms_primary_email(): void
    {
        $token = 'valid_token';
        $user = $this->createUser('user@example.com');
        $verification = (object) [
            'user_id' => 1,
            'new_email' => 'user@example.com',
            'expires_at' => now()->addHour(),
        ];

        $this->userRepo->shouldReceive('findEmailVerificationByToken')->with($token)->once()->andReturn($verification);
        $this->userRepo->shouldReceive('findById')->with(1)->once()->andReturn($user);
        $this->userRepo->shouldReceive('save')->once()->with($user);
        $this->userRepo->shouldReceive('deleteEmailVerification')->once()->with($token);
        $this->expectConsent();

        $this->useCase->execute($token, true);

        $this->assertTrue($user->isEmailVerified());
        $this->assertEquals('user@example.com', (string) $user->email);
    }

    public function test_confirms_email_change(): void
    {
        $token = 'change_token';
        $user = $this->createUser('old@example.com');
        $verification = (object) [
            'user_id' => 1,
            'new_email' => 'new@example.com',
            'expires_at' => now()->addHour(),
        ];

        $this->userRepo->shouldReceive('findEmailVerificationByToken')->with($token)->once()->andReturn($verification);
        $this->userRepo->shouldReceive('findById')->with(1)->once()->andReturn($user);
        $this->userRepo->shouldReceive('save')->once()->with($user);
        $this->userRepo->shouldReceive('deleteEmailVerification')->once()->with($token);
        $this->expectConsent();

        $this->useCase->execute($token, true);

        $this->assertTrue($user->isEmailVerified());
        $this->assertEquals('new@example.com', (string) $user->email);
    }

    public function test_throws_exception_if_token_expired(): void
    {
        $token = 'expired_token';
        $verification = (object) [
            'user_id' => 1,
            'new_email' => 'any@example.com',
            'expires_at' => now()->subMinute(),
        ];
        $this->userRepo->shouldReceive('findEmailVerificationByToken')->with($token)->once()->andReturn($verification);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Токен недействителен или срок его действия истёк');
        $this->useCase->execute($token, true);
    }

    public function test_throws_exception_if_token_not_found(): void
    {
        $this->userRepo->shouldReceive('findEmailVerificationByToken')->with('bad_token')->once()->andReturn(null);
        $this->expectException(InvalidArgumentException::class);
        $this->useCase->execute('bad_token', true);
    }

    public function test_throws_exception_if_user_not_found(): void
    {
        $token = 'token';
        $verification = (object) ['user_id' => 99, 'new_email' => 'x@x.com', 'expires_at' => now()->addHour()];
        $this->userRepo->shouldReceive('findEmailVerificationByToken')->once()->andReturn($verification);
        $this->userRepo->shouldReceive('findById')->with(99)->once()->andReturn(null);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Пользователь не найден');
        $this->useCase->execute($token, true);
    }

    public function test_throws_exception_without_agreement(): void
    {
        $token = 'token';
        $verification = (object) ['user_id' => 1, 'new_email' => 'x@x.com', 'expires_at' => now()->addHour()];
        $this->userRepo->shouldReceive('findEmailVerificationByToken')->once()->andReturn($verification);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Нет согласия');
        $this->useCase->execute($token, false);
    }
}
