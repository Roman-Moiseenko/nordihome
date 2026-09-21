<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\ProductPrice;

use App\Modules\Accounting\Application\Actions\ProductPrice\SetPriceUseCase;
use App\Modules\Accounting\Application\DTOs\ProductPrice\SetProductPriceData;
use App\Modules\Accounting\Domain\Entities\ProductPriceEntity;
use App\Modules\Accounting\Domain\ValueObjects\PriceType;
use App\Modules\Accounting\Infrastructure\Interfaces\PriceRepositoryInterface;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SetProductPriceUseCaseTest extends TestCase
{
    private PriceRepositoryInterface $priceRepository;
    private SetPriceUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->priceRepository = Mockery::mock(PriceRepositoryInterface::class);
        $this->useCase = new SetPriceUseCase($this->priceRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_creates_and_saves_price(): void
    {
        $this->priceRepository->shouldReceive('getLastByProductAndType')
            ->with(5, 'retail')
            ->once()
            ->andReturn(null);

        $this->priceRepository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(fn(ProductPriceEntity $price) => $price->productId === 5
                && $price->price === 1999.90
                && $price->priceType->value === PriceType::RETAIL))
            ->andReturnUsing(fn(ProductPriceEntity $price) => $price);

        $dto = new SetProductPriceData(productId: 5, price: 1999.90, priceType: 'retail', founded: 'src', comment: 'note');

        $result = $this->useCase->execute($dto);

        $this->assertInstanceOf(ProductPriceEntity::class, $result);
        $this->assertSame('src', $result->founded);
        $this->assertSame('note', $result->comment);
    }
}
