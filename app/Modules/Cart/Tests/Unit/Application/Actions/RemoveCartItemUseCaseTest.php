<?php

namespace App\Modules\Cart\Tests\Unit\Application\Actions;

use App\Modules\Cart\Application\Actions\RemoveCartItemUseCase;
use App\Modules\Cart\Domain\Interfaces\CartRepositoryInterface;
use App\Modules\Shop\Application\DTOs\ClientContext;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RemoveCartItemUseCaseTest extends TestCase
{
    private CartRepositoryInterface $cartRepository;
    private RemoveCartItemUseCase $useCase;
    private ClientContext $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartRepository = Mockery::mock(CartRepositoryInterface::class);
        $this->useCase = new RemoveCartItemUseCase($this->cartRepository);
        $this->client = new ClientContext(uuid: 'test-uuid');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_removes_item(): void
    {
        $this->cartRepository
            ->shouldReceive('removeByProductId')
            ->with(10, $this->client)
            ->once();

        $this->assertSame(0, $this->useCase->execute(10, $this->client));
    }
}
