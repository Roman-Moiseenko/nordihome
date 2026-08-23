<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\Category;

use App\Modules\Catalog\Application\Actions\Category\ViewCategoryUseCase;
use App\Modules\Catalog\Application\Interfaces\CategoryRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\CategoryEntity;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class ViewCategoryUseCaseTest extends TestCase
{
    use MockPermission;

    private CategoryRepositoryInterface $categoryRepository;
    private ViewCategoryUseCase $useCase;

    public function getModuleName(): string
    {
        return 'catalog';
    }

    public function getEntityName(): string
    {
        return 'category';
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->categoryRepository = Mockery::mock(CategoryRepositoryInterface::class);
        $this->useCase = new ViewCategoryUseCase($this->categoryRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_returns_entity(): void
    {
        $entity = new CategoryEntity('Мебель', new Slug('Мебель'));
        $entity->id = 5;

        $this->categoryRepository->shouldReceive('getById')->with(5)->once()->andReturn($entity);

        $this->assertSame($entity, $this->useCase->execute(5, $this->mockUserPermission(view: true)));
    }

    #[Test]
    public function it_throws_access_denied_when_missing_permission(): void
    {
        $this->categoryRepository->shouldNotReceive('getById');

        $this->expectException(\DomainException::class);
        $this->useCase->execute(5, $this->mockUserPermission(view: false));
    }
}
