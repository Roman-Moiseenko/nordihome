<?php

namespace App\Modules\Order\Tests\Unit\Application\Actions\AdditionGuide;

use App\Modules\Guide\Domain\Entities\AdditionEntity;
use App\Modules\Guide\Domain\Interfaces\AdditionRepositoryInterface;
use App\Modules\Guide\Domain\ValueObjects\AdditionType;
use App\Modules\Order\Application\Actions\AdditionGuide\GetDeliveryAdditionUseCase;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Mockery;
use PHPUnit\Framework\TestCase;

class GetDeliveryAdditionUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_returns_koenig_for_region_39(): void
    {
        $repository = Mockery::mock(AdditionRepositoryInterface::class);
        $addition = new AdditionEntity('Доставка Калининград', new Slug('koenig'), new AdditionType(null));
        $repository->shouldReceive('findBySlug')->with('koenig')->once()->andReturn($addition);

        $useCase = new GetDeliveryAdditionUseCase($repository);

        $this->assertSame($addition, $useCase->execute(39));
    }

    public function test_returns_russia_for_other_regions(): void
    {
        $repository = Mockery::mock(AdditionRepositoryInterface::class);
        $addition = new AdditionEntity('Доставка Россия', new Slug('russia'), new AdditionType(null));
        $repository->shouldReceive('findBySlug')->with('russia')->once()->andReturn($addition);

        $useCase = new GetDeliveryAdditionUseCase($repository);

        $this->assertSame($addition, $useCase->execute(77));
    }
}
