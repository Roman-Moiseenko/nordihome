<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\Category;

use App\Modules\Catalog\Application\Actions\Category\FindOrCreateTempCategory;
use App\Modules\Catalog\Domain\Entities\CategoryEntity;
use App\Modules\Catalog\Domain\Interfaces\CategoryRepositoryInterface;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FindOrCreateTempCategoryTest extends TestCase
{
    private CategoryRepositoryInterface $categoryRepository;
    private FindOrCreateTempCategory $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->categoryRepository = Mockery::mock(CategoryRepositoryInterface::class);
        $this->useCase = new FindOrCreateTempCategory($this->categoryRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_returns_existing_temp_category(): void
    {
        $category = new CategoryEntity('!!! ТОВАРЫ ИКЕА ПАРСЕР !!!', new Slug(CategoryEntity::TEMP_IKEA));
        $category->id = 1;

        $this->categoryRepository->shouldReceive('getBySlug')->with(CategoryEntity::TEMP_IKEA)->once()->andReturn($category);
        $this->categoryRepository->shouldNotReceive('save');

        $this->assertSame($category, $this->useCase->execute());
    }

    #[Test]
    public function it_creates_temp_category_when_missing(): void
    {
        $this->categoryRepository->shouldReceive('getBySlug')->with(CategoryEntity::TEMP_IKEA)->once()->andReturn(null);
        $this->categoryRepository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(fn(CategoryEntity $category) => $category->slug->getValue() === CategoryEntity::TEMP_IKEA))
            ->andReturnUsing(fn(CategoryEntity $category) => $category);

        $result = $this->useCase->execute();

        $this->assertInstanceOf(CategoryEntity::class, $result);
        $this->assertFalse($result->isPublished());
    }
}
