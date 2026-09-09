<?php

declare(strict_types=1);

namespace App\Modules\Content\Tests\Unit\Application\Actions\Label;

use App\Modules\Content\Application\Actions\Label\CreateLabelUseCase;
use App\Modules\Content\Application\DTOs\Label\LabelCreateData;
use App\Modules\Content\Domain\Entities\LabelEntity;
use App\Modules\Content\Domain\Interfaces\LabelRepositoryInterface;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class CreateLabelUseCaseTest extends TestCase
{
    use MockPermission;

    private LabelRepositoryInterface $repository;
    private CreateLabelUseCase $useCase;

    public function getModuleName(): string
    {
        return 'content';
    }

    public function getEntityName(): string
    {
        return 'label';
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(LabelRepositoryInterface::class);
        $this->useCase = new CreateLabelUseCase($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_creates_label_with_slug(): void
    {
        $this->repository->shouldReceive('existsSlug')->with('novinka')->once()->andReturn(false);
        $this->repository->shouldReceive('save')->once()->andReturnUsing(
            fn(LabelEntity $label) => $label
        );

        $dto = new LabelCreateData(name: 'Новинка', slug: null);

        $result = $this->useCase->execute($dto, $this->mockUserPermission(create: true));

        $this->assertSame('Новинка', $result->name);
        $this->assertSame('novinka', $result->slug->getValue());
    }

    #[Test]
    public function it_appends_suffix_when_slug_exists(): void
    {
        $this->repository->shouldReceive('existsSlug')->with('novinka')->once()->andReturn(true);
        $this->repository->shouldReceive('save')->once()->andReturnUsing(
            fn(LabelEntity $label) => $label
        );

        $dto = new LabelCreateData(name: 'Новинка', slug: null);

        $result = $this->useCase->execute($dto, $this->mockUserPermission(create: true));

        $this->assertSame('Новинка', $result->name);
        $this->assertStringStartsWith('novinka-', $result->slug->getValue());
    }

    #[Test]
    public function it_throws_access_denied_when_missing_permission(): void
    {
        $this->repository->shouldNotReceive('existsSlug');

        $dto = new LabelCreateData(name: 'Новинка', slug: null);

        $this->expectException(AccessDeniedException::class);
        $this->useCase->execute($dto, $this->mockUserPermission(create: false));
    }
}
