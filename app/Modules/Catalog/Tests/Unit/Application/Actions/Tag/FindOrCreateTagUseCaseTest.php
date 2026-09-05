<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\Tag;

use App\Modules\Catalog\Application\Actions\Tag\FindOrCreateTagUseCase;
use App\Modules\Catalog\Domain\Entities\TagEntity;
use App\Modules\Catalog\Domain\Interfaces\TagRepositoryInterface;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FindOrCreateTagUseCaseTest extends TestCase
{
    private TagRepositoryInterface $repository;
    private FindOrCreateTagUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(TagRepositoryInterface::class);
        $this->useCase = new FindOrCreateTagUseCase($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_returns_existing_tag(): void
    {
        $tag = new TagEntity('Скидка', new Slug('Скидка'));
        $tag->id = 3;

        $this->repository->shouldReceive('findByName')->with('Скидка')->once()->andReturn($tag);
        $this->repository->shouldNotReceive('save');

        $this->assertSame($tag, $this->useCase->execute('Скидка'));
    }

    #[Test]
    public function it_creates_tag_when_missing(): void
    {
        $this->repository->shouldReceive('findByName')->with('Хит')->once()->andReturn(null);
        $this->repository->shouldReceive('existsSlug')->with('hit', 0)->once()->andReturn(false);
        $this->repository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(fn(TagEntity $tag) => $tag->name === 'Хит'))
            ->andReturnUsing(fn(TagEntity $tag) => $tag);

        $result = $this->useCase->execute('Хит');

        $this->assertInstanceOf(TagEntity::class, $result);
        $this->assertSame('Хит', $result->name);
    }
}
