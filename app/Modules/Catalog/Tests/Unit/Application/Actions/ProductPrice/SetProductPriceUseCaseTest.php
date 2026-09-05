<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\ProductPrice;

use App\Modules\Catalog\Application\Actions\ProductPrice\SetProductPriceUseCase;
use App\Modules\Catalog\Application\DTOs\ProductPrice\SetProductPriceData;
use App\Modules\Catalog\Domain\Entities\ProductPriceEntity;
use App\Modules\Catalog\Domain\Interfaces\ProductPriceRepositoryInterface;
use App\Modules\Catalog\Domain\ValueObjects\PriceType;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SetProductPriceUseCaseTest extends TestCase
{
    private ProductPriceRepositoryInterface $priceRepository;
    private SetProductPriceUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->priceRepository = Mockery::mock(ProductPriceRepositoryInterface::class);
        $this->useCase = new SetProductPriceUseCase($this->priceRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_creates_and_saves_price(): void
    {
        $this->priceRepository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(fn(ProductPriceEntity $price) => $price->productId === 5
                && $price->price === 1999.90
                && $price->priceType->value === PriceType::RETAIL))
            ->andReturnUsing(fn(ProductPriceEntity $price) => $price);

        $dto = new SetProductPriceData(productId: 5, price: 1999.90, priceType: 'retail', founded: 'src', comment: 'note');

        $result = $this->useCase->execute($dto, new UserPermission(null, [], []));

        $this->assertInstanceOf(ProductPriceEntity::class, $result);
        $this->assertSame('src', $result->founded);
        $this->assertSame('note', $result->comment);
    }
}
