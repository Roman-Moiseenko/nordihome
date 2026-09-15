<?php

namespace App\Modules\Cart\Tests\Unit\Application\Actions;

use App\Modules\Cart\Application\Actions\AddToCartUseCase;
use App\Modules\Cart\Application\DTOs\AddProductToCartData;
use App\Modules\Cart\Domain\Entities\CartItemEntity;
use App\Modules\Cart\Domain\Interfaces\CartRepositoryInterface;
use App\Modules\Shop\Application\DTOs\ClientContext;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AddToCartUseCaseTest extends TestCase
{
    private CartRepositoryInterface $cartRepository;
    private AddToCartUseCase $useCase;
    private ClientContext $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartRepository = Mockery::mock(CartRepositoryInterface::class);
        $this->useCase = new AddToCartUseCase($this->cartRepository);
        $this->client = new ClientContext(uuid: 'test-uuid');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_adds_new_item_to_cart(): void
    {
        $this->cartRepository
            ->shouldReceive('getItemByProductId')
            ->with(10, $this->client)
            ->once()
            ->andReturnNull();

        $this->cartRepository
            ->shouldReceive('save')
            ->once()
            ->with(
                Mockery::on(fn(CartItemEntity $item) => $item->productId === 10
                    && $item->quantity === 2.0
                    && $item->isParser === false),
                $this->client
            )
            ->andReturnUsing(fn(CartItemEntity $item) => $item);

        $dto = new AddProductToCartData(id: 10, quantity: 2, isParser: false);

        $this->useCase->execute($dto, $this->client);
        $this->addToAssertionCount(1);
    }

    #[Test]
    public function it_increments_quantity_when_item_already_exists(): void
    {
        $existing = new CartItemEntity(productId: 10, quantity: 1.0, isParser: false);

        $this->cartRepository
            ->shouldReceive('getItemByProductId')
            ->with(10, $this->client)
            ->once()
            ->andReturn($existing);

        $this->cartRepository
            ->shouldReceive('save')
            ->once()
            ->with(
                Mockery::on(fn(CartItemEntity $item) => $item->quantity === 4.0),
                $this->client
            )
            ->andReturnUsing(fn(CartItemEntity $item) => $item);

        $dto = new AddProductToCartData(id: 10, quantity: 3, isParser: false);

        $this->useCase->execute($dto, $this->client);
        $this->addToAssertionCount(1);
    }
}
