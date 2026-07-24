<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Application\Actions\Tag\CreateTagUseCase;
use App\Modules\Catalog\Application\Actions\Tag\IndexTagUseCase;
use App\Modules\Catalog\Application\Actions\Tag\RemoveTagUseCase;
use App\Modules\Catalog\Application\Actions\Tag\UpdateTagUseCase;
use App\Modules\Catalog\Application\Actions\Tag\ViewTagUseCase;
use App\Modules\Catalog\Application\DTOs\Tag\TagCreateData;
use App\Modules\Catalog\Application\DTOs\Tag\TagUpdateData;
use App\Modules\Catalog\Application\DTOs\Tag\TagViewData;
use App\Modules\Catalog\Infrastructure\Models\Tag;
use App\Modules\Catalog\Repository\TagRepository;
use App\Modules\Catalog\Service\TagService;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TagController extends Controller
{
    private TagService $service;
    private TagRepository $repository;

    public function __construct(
        TagService                        $service,
        TagRepository                     $repository,
        private readonly IndexTagUseCase  $indexTagUseCase,
        private readonly CreateTagUseCase $createTagUseCase,
        private readonly UpdateTagUseCase $updateTagUseCase,
        private readonly RemoveTagUseCase $removeTagUseCase,
        private readonly ViewTagUseCase   $viewTagUseCase,
    )
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request, UserPermission $userPermission): Response
    {
        $data = $this->indexTagUseCase->execute($userPermission);
        return Inertia::render('Catalog/Tag/Index', [
            'tags' => $data,
        ]);
    }
    public function show(int $id, Request $request, UserPermission $userPermission): Response
    {

        $tagEntity = $this->viewTagUseCase->execute($id, $userPermission);
        return Inertia::render('Catalog/Tag/Show', [
            'tag' => TagViewData::fromEntity($tagEntity),
        ]);
    }

    public function store(Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = TagCreateData::validateAndCreate($request->all());
        $this->createTagUseCase->execute($dto, $userPermission);
        return redirect()->back()->with('success', 'Метка создана');
    }

    public function update(int $id, Request $request, UserPermission $userPermission): RedirectResponse
    {
        \Log::info(json_encode($request->all()));
        $dto = TagUpdateData::validateAndCreate($request->all());
        $this->updateTagUseCase->execute($id, $dto, $userPermission);

        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(int $id, UserPermission $userPermission): RedirectResponse
    {
        $this->removeTagUseCase->execute($id, $userPermission);

        return redirect()->back()->with('success', 'Метка удалена');
    }
}
