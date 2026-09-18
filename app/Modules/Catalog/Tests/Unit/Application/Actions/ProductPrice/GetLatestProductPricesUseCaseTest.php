<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\ProductPrice;

use App\Modules\Accounting\Application\Actions\ProductPrice\GetLatestPricesQuery;
use App\Modules\Accounting\Infrastructure\Interfaces\PriceRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GetLatestProductPricesUseCaseTest extends TestCase
{
    private PriceRepositoryInterface $priceRepository;
    private GetLatestPricesQuery $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->priceRepository = Mockery::mock(PriceRepositoryInterface::class);
        $this->useCase = new GetLatestPricesQuery($this->priceRepository);
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

        $this->expectException(AccessDeniedException::class);

        $this->useCase->execute(5, new UserPermission(null, [], []));
    }
}
