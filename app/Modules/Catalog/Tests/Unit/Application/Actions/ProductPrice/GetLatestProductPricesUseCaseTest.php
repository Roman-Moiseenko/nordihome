<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\ProductPrice;

use App\Modules\Catalog\Application\Actions\ProductPrice\GetLatestProductPricesUseCase;
use App\Modules\Catalog\Application\Interfaces\ProductPriceRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GetLatestProductPricesUseCaseTest extends TestCase
{
    private ProductPriceRepositoryInterface $priceRepository;
    private GetLatestProductPricesUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->priceRepository = Mockery::mock(ProductPriceRepositoryInterface::class);
        $this->useCase = new GetLatestProductPricesUseCase($this->priceRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_returns_current_prices(): void
    {
        $prices = ['retail' => 100.0, 'bulk' => 80.0];

        $this->priceRepository->shouldReceive('findCurrentPrices')->with(5)->once()->andReturn($prices);

        $permission = new UserPermission(null, [], ['catalog.product.price.view']);

        $this->assertSame($prices, $this->useCase->execute(5, $permission));
    }

    #[Test]
    public function it_throws_access_denied_when_missing_permission(): void
    {
        $this->priceRepository->shouldNotReceive('findCurrentPrices');

        $this->expectException(\DomainException::class);

        $this->useCase->execute(5, new UserPermission(null, [], []));
    }
}
