<?php

declare(strict_types=1);

namespace App\Modules\Content\Tests\Unit\Application\Actions\Label;

use App\Modules\Content\Application\Actions\Label\UpdateLabelUseCase;
use App\Modules\Content\Application\DTOs\Label\LabelUpdateData;
use App\Modules\Content\Domain\Entities\LabelEntity;
use App\Modules\Content\Domain\Interfaces\LabelRepositoryInterface;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class UpdateLabelUseCaseTest extends TestCase
{
    use MockPermission;

    private LabelRepositoryInterface $repository;
    private UpdateLabelUseCase $useCase;

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
        $this->useCase = new UpdateLabelUseCase($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_updates_name_and_slug(): void
    {
        $label = new LabelEntity('Старое', new Slug('staroe'));
        $label->id = 5;

        $this->repository->shouldReceive('getById')->with(5)->once()->andReturn($label);
        $this->repository->shouldReceive('existsSlug')->with('new-label', 5)->once()->andReturn(false);
        $this->repository->shouldReceive('save')->once()->with($label)->andReturn($label);

        $dto = new LabelUpdateData(name: 'Акция', slug: 'new-label');

        $result = $this->useCase->execute(5, $dto, $this->mockUserPermission(edit: true));

        $this->assertSame('Акция', $result->name);
        $this->assertSame('new-label', $result->slug->getValue());
    }

    #[Test]
    public function it_throws_access_denied_when_missing_permission(): void
    {
        $this->repository->shouldNotReceive('getById');

        $dto = new LabelUpdateData(name: 'Акция', slug: null);

        $this->expectException(AccessDeniedException::class);
        $this->useCase->execute(5, $dto, $this->mockUserPermission(edit: false));
    }
}
