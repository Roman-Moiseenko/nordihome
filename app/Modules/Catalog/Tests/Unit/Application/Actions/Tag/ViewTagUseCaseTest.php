<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\Tag;

use App\Modules\Catalog\Application\Actions\Tag\ViewTagUseCase;
use App\Modules\Catalog\Application\Interfaces\TagRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\TagEntity;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class ViewTagUseCaseTest extends TestCase
{
    use MockPermission;

    private TagRepositoryInterface $repository;
    private ViewTagUseCase $useCase;

    public function getModuleName(): string
    {
        return 'catalog';
    }

    public function getEntityName(): string
    {
        return 'product';
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(TagRepositoryInterface::class);
        $this->useCase = new ViewTagUseCase($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_returns_entity(): void
    {
        $entity = new TagEntity('Скидка', new Slug('Скидка'));
        $entity->id = 5;

        $this->repository->shouldReceive('getById')->with(5)->once()->andReturn($entity);

        $this->assertSame($entity, $this->useCase->execute(5, $this->mockUserPermission(view: true)));
    }

    #[Test]
    public function it_throws_access_denied_when_missing_permission(): void
    {
        $this->repository->shouldNotReceive('getById');

        $this->expectException(\DomainException::class);
        $this->useCase->execute(5, $this->mockUserPermission(view: false));
    }
}
