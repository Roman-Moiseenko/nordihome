<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\Brand;

use App\Modules\Catalog\Application\Actions\Brand\FindOrCreateBrandUseCase;
use App\Modules\Catalog\Application\Interfaces\BrandRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\BrandEntity;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FindOrCreateBrandUseCaseTest extends TestCase
{
    private BrandRepositoryInterface $brandRepository;
    private FindOrCreateBrandUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->brandRepository = Mockery::mock(BrandRepositoryInterface::class);
        $this->useCase = new FindOrCreateBrandUseCase($this->brandRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_returns_existing_brand(): void
    {
        $brand = new BrandEntity('Икеа');
        $brand->id = 1;

        $this->brandRepository->shouldReceive('getByName')->with('Икеа')->once()->andReturn($brand);
        $this->brandRepository->shouldNotReceive('save');

        $this->assertSame($brand, $this->useCase->execute('Икеа'));
    }

    #[Test]
    public function it_creates_brand_when_missing(): void
    {
        $this->brandRepository->shouldReceive('getByName')->with('NONAME')->once()->andReturn(null);
        $this->brandRepository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(fn(BrandEntity $brand) => $brand->name === 'NONAME'))
            ->andReturnUsing(fn(BrandEntity $brand) => $brand);

        $result = $this->useCase->execute('NONAME');

        $this->assertInstanceOf(BrandEntity::class, $result);
        $this->assertSame('NONAME', $result->name);
    }
}
