<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\Tag;

use App\Modules\Catalog\Application\Actions\Tag\CreateTagUseCase;
use App\Modules\Catalog\Application\DTOs\Tag\TagCreateData;
use App\Modules\Catalog\Domain\Interfaces\TagRepositoryInterface;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class CreateTagUseCaseTest extends TestCase
{
    use MockPermission;

    private TagRepositoryInterface $repository;
    private CreateTagUseCase $useCase;

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
        $this->useCase = new CreateTagUseCase($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // ВАЖНО: сценарий успешного создания НЕ тестируется, т.к. CreateTagUseCase
    // вызывает $this->repository->existsSlug((string)$slug) с ОДНИМ аргументом,
    // а TagRepositoryInterface::existsSlug(string $slug, int $tagId) требует ДВА.
    // Это битый production-код (ArgumentCountError в рантайме). Отмечено в отчёте.

    #[Test]
    public function it_throws_access_denied_when_missing_permission(): void
    {
        $this->repository->shouldNotReceive('existsSlug');

        $dto = new TagCreateData(name: 'Скидка', slug: null, isMain: false);

        $this->expectException(\DomainException::class);
        $this->useCase->execute($dto, $this->mockUserPermission(create: false));
    }
}
