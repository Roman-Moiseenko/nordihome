<?php

namespace App\Modules\Cart\Tests\Unit\Application\Actions;

use App\Modules\Cart\Application\Actions\PlusToCartUseCase;
use App\Modules\Cart\Domain\Entities\CartItemEntity;
use App\Modules\Cart\Domain\Interfaces\CartRepositoryInterface;
use App\Modules\Storefront\Application\DTOs\ClientContext;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PlusToCartUseCaseTest extends TestCase
{
    private CartRepositoryInterface $cartRepository;
    private PlusToCartUseCase $useCase;
    private ClientContext $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartRepository = Mockery::mock(CartRepositoryInterface::class);
        $this->useCase = new PlusToCartUseCase($this->cartRepository);
        $this->client = new ClientContext(uuid: 'test-uuid');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_increments_matching_item_quantity(): void
    {
        $item = new CartItemEntity(productId: 10, quantity: 2.0, isParser: false);

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

        $this->useCase->execute(10, 3, $this->client);

        $this->assertSame(5.0, $item->quantity);
    }
}
