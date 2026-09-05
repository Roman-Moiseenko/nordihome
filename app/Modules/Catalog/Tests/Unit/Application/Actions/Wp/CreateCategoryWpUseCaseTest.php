<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\Wp;

use App\Modules\Catalog\Application\Actions\Wp\CreateCategoryWpUseCase;
use App\Modules\Catalog\Application\DTOs\Wp\CategoryRoomWpData;
use App\Modules\Catalog\Domain\Entities\CategoryEntity;
use App\Modules\Catalog\Domain\Interfaces\CategoryRepositoryInterface;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class CreateCategoryWpUseCaseTest extends TestCase
{
    use MockPermission;

    private CategoryRepositoryInterface $categoryRepository;
    private CreateCategoryWpUseCase $useCase;

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
        $this->useCase = new CreateCategoryWpUseCase($this->categoryRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_creates_category_with_wp_id(): void
    {
        $this->categoryRepository->shouldReceive('existsByWpId')->with(42)->once()->andReturn(false);
        $this->categoryRepository->shouldReceive('existsSlug')->with('mebel')->once()->andReturn(false);
        $this->categoryRepository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(fn(CategoryEntity $category) => $category->wpId === 42 && $category->name === 'Мебель'))
            ->andReturnUsing(fn(CategoryEntity $category) => $category);

        $dto = new CategoryRoomWpData(wpId: 42, name: 'Мебель', parentId: null);

        $result = $this->useCase->execute($dto, $this->mockUserPermission(create: true));

        $this->assertInstanceOf(CategoryEntity::class, $result);
        $this->assertSame(42, $result->wpId);
    }

    #[Test]
    public function it_returns_null_when_wp_id_exists(): void
    {
        $this->categoryRepository->shouldReceive('existsByWpId')->with(42)->once()->andReturn(true);
        $this->categoryRepository->shouldNotReceive('save');

        $dto = new CategoryRoomWpData(wpId: 42, name: 'Мебель', parentId: null);

        $this->assertNull($this->useCase->execute($dto, $this->mockUserPermission(create: true)));
    }

    #[Test]
    public function it_throws_access_denied_when_missing_permission(): void
    {
        $this->categoryRepository->shouldNotReceive('existsByWpId');

        $dto = new CategoryRoomWpData(wpId: 42, name: 'Мебель', parentId: null);

        $this->expectException(\DomainException::class);
        $this->useCase->execute($dto, $this->mockUserPermission(create: false));
    }
}
