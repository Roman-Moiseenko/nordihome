<?php

namespace App\Modules\Cart\Tests\Unit\Application\Actions;

use App\Modules\Cart\Application\Actions\SubToCartUseCase;
use App\Modules\Cart\Domain\Entities\CartItemEntity;
use App\Modules\Cart\Domain\Interfaces\CartRepositoryInterface;
use App\Modules\Storefront\Application\DTOs\ClientContext;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SubToCartUseCaseTest extends TestCase
{
    private CartRepositoryInterface $cartRepository;
    private SubToCartUseCase $useCase;
    private ClientContext $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartRepository = Mockery::mock(CartRepositoryInterface::class);
        $this->useCase = new SubToCartUseCase($this->cartRepository);
        $this->client = new ClientContext(uuid: 'test-uuid');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_decrements_matching_item_quantity(): void
    {
        $item = new CartItemEntity(productId: 10, quantity: 5.0, isParser: false);

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
        $this->cartRepository->shouldNotReceive('removeByProductId');

        $this->useCase->execute(10, 2, $this->client);

        $this->assertSame(3.0, $item->quantity);
    }

    #[Test]
    public function it_removes_item_when_quantity_is_less_or_equal(): void
    {
        $item = new CartItemEntity(productId: 10, quantity: 2.0, isParser: false);

        $this->cartRepository
            ->shouldReceive('getItemByProductId')
            ->with(10, $this->client)
            ->once()
            ->andReturn($item);
        $this->cartRepository
            ->shouldReceive('removeByProductId')
            ->with(10, $this->client)
            ->once();
        $this->cartRepository->shouldNotReceive('save');

        $this->useCase->execute(10, 3, $this->client);
        $this->addToAssertionCount(1);
    }
}
