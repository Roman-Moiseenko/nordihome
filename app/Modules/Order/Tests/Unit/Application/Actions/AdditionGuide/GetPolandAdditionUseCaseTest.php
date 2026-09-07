<?php

namespace App\Modules\Order\Tests\Unit\Application\Actions\AdditionGuide;

use App\Modules\Guide\Domain\Entities\AdditionEntity;
use App\Modules\Guide\Domain\Interfaces\AdditionRepositoryInterface;
use App\Modules\Guide\Domain\ValueObjects\AdditionType;
use App\Modules\Order\Application\Actions\AdditionGuide\GetPolandAdditionUseCase;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Mockery;
use PHPUnit\Framework\TestCase;

class GetPolandAdditionUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_returns_poland_addition_by_slug(): void
    {
        $repository = Mockery::mock(AdditionRepositoryInterface::class);
        $addition = new AdditionEntity('Доставка из Польши', new Slug('poland'), new AdditionType(null));
        $repository->shouldReceive('findBySlug')->with('poland')->once()->andReturn($addition);

        $useCase = new GetPolandAdditionUseCase($repository);

        $this->assertSame($addition, $useCase->execute());
    }

    public function test_returns_null_when_addition_not_found(): void
    {
        $repository = Mockery::mock(AdditionRepositoryInterface::class);
        $repository->shouldReceive('findBySlug')->with('poland')->once()->andReturnNull();

        $useCase = new GetPolandAdditionUseCase($repository);

        $this->assertNull($useCase->execute());
    }
}
