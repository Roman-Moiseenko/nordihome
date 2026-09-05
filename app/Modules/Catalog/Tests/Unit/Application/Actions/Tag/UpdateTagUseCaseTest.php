<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\Tag;

use App\Modules\Catalog\Application\Actions\Tag\UpdateTagUseCase;
use App\Modules\Catalog\Application\DTOs\Tag\TagUpdateData;
use App\Modules\Catalog\Domain\Entities\TagEntity;
use App\Modules\Catalog\Domain\Interfaces\TagRepositoryInterface;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class UpdateTagUseCaseTest extends TestCase
{
    use MockPermission;

    private TagRepositoryInterface $repository;
    private UpdateTagUseCase $useCase;

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
        $this->useCase = new UpdateTagUseCase($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_updates_fields_and_saves(): void
    {
        $tag = new TagEntity('Скидка', new Slug('Скидка'));
        $tag->id = 5;

        $this->repository->shouldReceive('getById')->with(5)->once()->andReturn($tag);
        $this->repository->shouldReceive('existsSlug')->with('new-tag', 5)->once()->andReturn(false);
        $this->repository->shouldReceive('save')->once()->with($tag)->andReturn($tag);

        $dto = new TagUpdateData(name: 'Акция', slug: 'new-tag', isMain: true);

        $result = $this->useCase->execute(5, $dto, $this->mockUserPermission(edit: true));

        $this->assertSame('Акция', $result->name);
        $this->assertSame('new-tag', $result->slug->getValue());
        $this->assertTrue($result->isMain);
    }

    #[Test]
    public function it_throws_access_denied_when_missing_permission(): void
    {
        $this->repository->shouldNotReceive('getById');

        $dto = new TagUpdateData(name: 'Акция', slug: null, isMain: false);

        $this->expectException(\DomainException::class);
        $this->useCase->execute(5, $dto, $this->mockUserPermission(edit: false));
    }
}
