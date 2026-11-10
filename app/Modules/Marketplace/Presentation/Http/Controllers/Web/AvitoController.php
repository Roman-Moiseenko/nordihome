<?php

namespace App\Modules\Marketplace\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Marketplace\Application\Actions\Avito\SyncAvitoCategoriesUseCase;
use App\Modules\Marketplace\Application\Actions\Avito\SyncAvitoCategoryFieldsUseCase;
use App\Modules\Marketplace\Application\Actions\Avito\TreeAvitoCategoryQuery;
use App\Modules\Marketplace\Application\Actions\AvitoProduct\CreateAvitoProductUseCase;
use App\Modules\Marketplace\Application\Actions\AvitoProduct\IndexAvitoProductQuery;
use App\Modules\Marketplace\Application\Actions\AvitoProduct\SetAvitoProductActiveUseCase;
use App\Modules\Marketplace\Application\Actions\AvitoProduct\ToggleAvitoProductDeliveryUseCase;
use App\Modules\Marketplace\Application\DTOs\AvitoProduct\AvitoProductCreateData;
use App\Modules\Marketplace\Application\DTOs\AvitoProduct\FilterAvitoProductIndexData;
use App\Modules\Marketplace\Domain\Interfaces\AvitoProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AvitoController extends Controller
{
    public function __construct(
        private readonly TreeAvitoCategoryQuery $treeAvitoCategoryQuery,
        private readonly SyncAvitoCategoriesUseCase $syncAvitoCategoriesUseCase,
        private readonly SyncAvitoCategoryFieldsUseCase $syncAvitoCategoryFieldsUseCase,
        private readonly IndexAvitoProductQuery $indexAvitoProductQuery,
        private readonly AvitoProductRepositoryInterface $avitoProductRepository,
        private readonly CreateAvitoProductUseCase $createAvitoProductUseCase,
        private readonly ToggleAvitoProductDeliveryUseCase $toggleAvitoProductDeliveryUseCase,
        private readonly SetAvitoProductActiveUseCase $setAvitoProductActiveUseCase,
    ) {}

    public function index(Request $request, UserPermission $userPermission): Response
    {
        $filter = FilterAvitoProductIndexData::validateAndCreate($request->all());
        $products = $this->indexAvitoProductQuery->execute($filter, $userPermission);

        $filter->active = $this->avitoProductRepository->countActive();
        $filter->archive = $this->avitoProductRepository->countArchived();

        return Inertia::render('Marketplace/Avito/Index', [
            'products' => $products,
            'filters' => $filter,
        ]);
    }

    public function categories(UserPermission $userPermission): Response
    {
        $categories = $this->treeAvitoCategoryQuery->execute($userPermission);

        return Inertia::render('Marketplace/Avito/Categories', [
            'categories' => $categories,
        ]);
    }

    public function settings(): Response
    {
        return Inertia::render('Marketplace/Avito/Settings');
    }

    public function operations(): Response
    {
        return Inertia::render('Marketplace/Avito/Operations');
    }

    public function productShow(int $id, UserPermission $userPermission): Response
    {
        if (!$userPermission->can('marketplace.avito.view')) {
            abort(403);
        }

        return Inertia::render('Marketplace/Avito/Show', [
            'id' => $id,
        ]);
    }

    public function store(Request $request, UserPermission $userPermission): RedirectResponse
    {
        $data = $request->all();
        if (!isset($data['productId']) && isset($data['product_id'])) {
            $data['productId'] = $data['product_id'];
        }

        $dto = AvitoProductCreateData::validateAndCreate($data);
        $product = $this->createAvitoProductUseCase->execute($dto, $userPermission);

        return redirect()->route('admin.marketplace.avito.product.show', $product->id)
            ->with('success', 'Товар добавлен в Авито');
    }

    public function toggleDelivery(int $id, UserPermission $userPermission): RedirectResponse
    {
        $this->toggleAvitoProductDeliveryUseCase->execute($id, $userPermission);

        return redirect()->back()->with('success', 'Сохранено');
    }

    public function setActive(int $id, Request $request, UserPermission $userPermission): RedirectResponse
    {
        $active = $request->boolean('active');
        $this->setAvitoProductActiveUseCase->execute($id, $active, $userPermission);

        return redirect()->back()->with('success', 'Сохранено');
    }

    public function syncCategories(UserPermission $userPermission): RedirectResponse
    {
        $this->syncAvitoCategoriesUseCase->execute($userPermission);

        return redirect()->back()->with('success', 'Категории синхронизированы');
    }

    public function syncFields(UserPermission $userPermission): RedirectResponse
    {
        $this->syncAvitoCategoryFieldsUseCase->execute($userPermission);

        return redirect()->back()->with('success', 'Параметры категорий синхронизированы');
    }
}
