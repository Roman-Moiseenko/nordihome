<?php

namespace App\Modules\Cart\Tests\Unit\Application\Actions;

use App\Modules\Cart\Application\Actions\SetToCartUseCase;
use App\Modules\Cart\Domain\Entities\CartItemEntity;
use App\Modules\Cart\Domain\Interfaces\CartRepositoryInterface;
use App\Modules\Storefront\Application\DTOs\ClientContext;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SetToCartUseCaseTest extends TestCase
{
    private CartRepositoryInterface $cartRepository;
    private SetToCartUseCase $useCase;
    private ClientContext $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartRepository = Mockery::mock(CartRepositoryInterface::class);
        $this->useCase = new SetToCartUseCase($this->cartRepository);
        $this->client = new ClientContext(uuid: 'test-uuid');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_removes_item_when_quantity_is_zero(): void
    {
        $this->cartRepository
            ->shouldReceive('removeByProductId')
            ->with(10, $this->client)
            ->once();
        $this->cartRepository->shouldNotReceive('getItemByProductId');
        $this->cartRepository->shouldNotReceive('save');

        $this->assertSame(0, $this->useCase->execute(10, 0, $this->client));
    }

    #[Test]
    public function it_sets_quantity_and_returns_it(): void
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

        $this->assertSame(5, $this->useCase->execute(10, 5, $this->client));
        $this->assertSame(5.0, $item->quantity);
    }
}
