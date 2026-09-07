<?php

namespace App\Modules\Order\Tests\Unit\Application\Actions\AdditionGuide;

use App\Modules\Guide\Domain\Entities\AdditionEntity;
use App\Modules\Guide\Domain\Interfaces\AdditionRepositoryInterface;
use App\Modules\Guide\Domain\ValueObjects\AdditionType;
use App\Modules\Order\Application\Actions\AdditionGuide\GetAssemblageAdditionUseCase;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Mockery;
use PHPUnit\Framework\TestCase;

class GetAssemblageAdditionUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_returns_assembly_addition_by_slug(): void
    {
        $repository = Mockery::mock(AdditionRepositoryInterface::class);
        $addition = new AdditionEntity('Сборка', new Slug('assembly-15'), new AdditionType(null));
        $repository->shouldReceive('findBySlug')->with('assembly-15')->once()->andReturn($addition);

        $useCase = new GetAssemblageAdditionUseCase($repository);

        $this->assertSame($addition, $useCase->execute());
    }

    public function test_returns_null_when_addition_not_found(): void
    {
        $repository = Mockery::mock(AdditionRepositoryInterface::class);
        $repository->shouldReceive('findBySlug')->with('assembly-15')->once()->andReturnNull();

        $useCase = new GetAssemblageAdditionUseCase($repository);

        $this->assertNull($useCase->execute());
    }
}
