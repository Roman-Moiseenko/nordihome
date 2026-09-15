<?php

namespace App\Modules\Cart\Tests\Unit\Application\Actions;

use App\Modules\Cart\Application\Actions\CheckAllToCartUseCase;
use App\Modules\Cart\Domain\Entities\CartItemEntity;
use App\Modules\Cart\Domain\Interfaces\CartRepositoryInterface;
use App\Modules\Shop\Application\DTOs\ClientContext;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CheckAllToCartUseCaseTest extends TestCase
{
    private CartRepositoryInterface $cartRepository;
    private CheckAllToCartUseCase $useCase;
    private ClientContext $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartRepository = Mockery::mock(CartRepositoryInterface::class);
        $this->useCase = new CheckAllToCartUseCase($this->cartRepository);
        $this->client = new ClientContext(uuid: 'test-uuid');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_marks_all_items_as_checked(): void
    {
        $item1 = new CartItemEntity(productId: 10, quantity: 1.0, isParser: false);
        $item1->check = false;
        $item2 = new CartItemEntity(productId: 20, quantity: 1.0, isParser: false);
        $item2->check = false;

        $this->cartRepository
            ->shouldReceive('getAll')
            ->with($this->client)
            ->once()
            ->andReturn([$item1, $item2]);
        $this->cartRepository->shouldReceive('save')->twice();

        $this->useCase->execute(true, $this->client);

        $this->assertTrue($item1->check);
        $this->assertTrue($item2->check);
    }

    #[Test]
    public function it_unmarks_all_items(): void
    {
        $item1 = new CartItemEntity(productId: 10, quantity: 1.0, isParser: false);
        $item1->check = true;
        $item2 = new CartItemEntity(productId: 20, quantity: 1.0, isParser: false);
        $item2->check = true;

        $this->cartRepository
            ->shouldReceive('getAll')
            ->with($this->client)
            ->once()
            ->andReturn([$item1, $item2]);
        $this->cartRepository->shouldReceive('save')->twice();

        $this->useCase->execute(false, $this->client);

        $this->assertFalse($item1->check);
        $this->assertFalse($item2->check);
    }

    #[Test]
    public function it_does_nothing_when_cart_is_empty(): void
    {
        $this->cartRepository
            ->shouldReceive('getAll')
            ->with($this->client)
            ->once()
            ->andReturn([]);
        $this->cartRepository->shouldNotReceive('save');

        $this->useCase->execute(true, $this->client);
        $this->addToAssertionCount(1);
    }
}
