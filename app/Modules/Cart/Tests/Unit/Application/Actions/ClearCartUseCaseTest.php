<?php

namespace App\Modules\Cart\Tests\Unit\Application\Actions;

use App\Modules\Cart\Application\Actions\ClearCartUseCase;
use App\Modules\Cart\Domain\Interfaces\CartRepositoryInterface;
use App\Modules\Shop\Application\DTOs\ClientContext;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ClearCartUseCaseTest extends TestCase
{
    private CartRepositoryInterface $cartRepository;
    private ClearCartUseCase $useCase;
    private ClientContext $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartRepository = Mockery::mock(CartRepositoryInterface::class);
        $this->useCase = new ClearCartUseCase($this->cartRepository);
        $this->client = new ClientContext(uuid: 'test-uuid');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_clears_cart(): void
    {
        $this->cartRepository
            ->shouldReceive('clearCart')
            ->with($this->client)
            ->once();

        $this->useCase->execute($this->client);
        $this->addToAssertionCount(1);
    }
}
