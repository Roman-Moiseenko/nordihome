<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\Tag;

use App\Modules\Catalog\Application\Actions\Tag\IndexTagUseCase;
use App\Modules\Catalog\Application\DTOs\Tag\TagIndexData;
use App\Modules\Catalog\Domain\Entities\TagEntity;
use App\Modules\Catalog\Domain\Interfaces\TagProductRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\TagRepositoryInterface;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class IndexTagUseCaseTest extends TestCase
{
    use MockPermission;

    private TagRepositoryInterface $tagRepository;
    private TagProductRepositoryInterface $tagProductRepository;
    private IndexTagUseCase $useCase;

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
        $this->tagRepository = Mockery::mock(TagRepositoryInterface::class);
        $this->tagProductRepository = Mockery::mock(TagProductRepositoryInterface::class);
        $this->useCase = new IndexTagUseCase($this->tagRepository, $this->tagProductRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_returns_paginated_tag_dtos_with_counts(): void
    {
        $tag = new TagEntity('Скидка', new Slug('Скидка'));
        $tag->id = 3;

        $paginator = new LengthAwarePaginator(new Collection([$tag]), 1, 20, 1);

        $this->tagRepository->shouldReceive('paginate')->with(20)->once()->andReturn($paginator);
        $this->tagProductRepository->shouldReceive('countProductsByTagIds')->with([3])->once()->andReturn([3 => 5]);

        $result = $this->useCase->execute($this->mockUserPermission(view: true), 20);

        $this->assertInstanceOf(TagIndexData::class, $result->getCollection()->first());
        $this->assertSame(3, $result->getCollection()->first()->id);
        $this->assertSame(5, $result->getCollection()->first()->count);
    }

    #[Test]
    public function it_throws_access_denied_when_missing_permission(): void
    {
        $this->tagRepository->shouldNotReceive('paginate');

        $this->expectException(\DomainException::class);
        $this->useCase->execute($this->mockUserPermission(view: false), 20);
    }
}
