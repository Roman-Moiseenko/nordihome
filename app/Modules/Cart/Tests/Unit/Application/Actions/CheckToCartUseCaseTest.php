<?php

namespace App\Modules\Cart\Tests\Unit\Application\Actions;

use App\Modules\Cart\Application\Actions\CheckToCartUseCase;
use App\Modules\Cart\Domain\Entities\CartItemEntity;
use App\Modules\Cart\Domain\Interfaces\CartRepositoryInterface;
use App\Modules\Shop\Application\DTOs\ClientContext;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CheckToCartUseCaseTest extends TestCase
{
    private CartRepositoryInterface $cartRepository;
    private CheckToCartUseCase $useCase;
    private ClientContext $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartRepository = Mockery::mock(CartRepositoryInterface::class);
        $this->useCase = new CheckToCartUseCase($this->cartRepository);
        $this->client = new ClientContext(uuid: 'test-uuid');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_unchecks_item(): void
    {
        $item = new CartItemEntity(productId: 10, quantity: 1.0, isParser: false);
        $item->check = true;

        $this->cartRepository
            ->shouldReceive('getItemByProductId')
            ->with(10, $this->client)
            ->once()
            ->andReturn($item);
        $this->cartRepository
            ->shouldReceive('save')
            ->once()
            ->with($item, $this->client)
            ->andReturnUsing(fn(CartItemEntity $item) => $item);

        $this->useCase->execute(10, $this->client);

        $this->assertFalse($item->check);
    }

    #[Test]
    public function it_checks_item(): void
    {
        $item = new CartItemEntity(productId: 10, quantity: 1.0, isParser: false);
        $item->check = false;

        $this->cartRepository
            ->shouldReceive('getItemByProductId')
            ->with(10, $this->client)
            ->once()
            ->andReturn($item);
        $this->cartRepository
            ->shouldReceive('save')
            ->once()
            ->with($item, $this->client)
            ->andReturnUsing(fn(CartItemEntity $item) => $item);

        $this->useCase->execute(10, $this->client);

        $this->assertTrue($item->check);
    }
}
