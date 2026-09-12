<?php

namespace App\Modules\Cart\Tests\Unit\Application\Actions;

use App\Modules\Cart\Application\Actions\ClearCartUseCase;
use App\Modules\Cart\Infrastructure\Persistence\CartRepository;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ClearCartUseCaseTest extends TestCase
{
    private CartRepository $storage;
    private ClearCartUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->storage = Mockery::mock(CartRepository::class);
        $this->useCase = new ClearCartUseCase($this->storage);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_clears_storage(): void
    {
        $this->storage->shouldReceive('clear')->once();

        $this->useCase->execute();
        $this->addToAssertionCount(1);
    }
}
