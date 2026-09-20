<?php

namespace App\Modules\Output\Presentation\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Output\Application\Actions\Feed\CreateFeedUseCase;
use App\Modules\Output\Application\Actions\Feed\IndexFeedQuery;
use App\Modules\Output\Application\Actions\Feed\RemoveFeedUseCase;
use App\Modules\Output\Application\Actions\Feed\UpdateFeedUseCase;
use App\Modules\Output\Application\Actions\Feed\ViewFeedQuery;
use App\Modules\Output\Application\DTOs\Feed\FeedCreateData;
use App\Modules\Output\Application\DTOs\Feed\FeedUpdateData;
use App\Modules\Output\Infrastructure\Models\Feed;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FeedController extends Controller
{
    public function __construct(
        private readonly IndexFeedQuery $indexFeedQuery,
        private readonly ViewFeedQuery $viewFeedQuery,
        private readonly CreateFeedUseCase $createFeedUseCase,
        private readonly UpdateFeedUseCase $updateFeedUseCase,
        private readonly RemoveFeedUseCase $removeFeedUseCase,
    ) {}

    public function index(Request $request, UserPermission $userPermission): Response
    {
        $feeds = $this->indexFeedQuery->execute($userPermission, $request->integer('size', 15));

        return Inertia::render('Output/Feed/Index', [
            'feeds' => $feeds,
        ]);
    }

    public function show(int $id, UserPermission $userPermission): Response
    {
        $feed = $this->viewFeedQuery->execute($id, $userPermission);

        return Inertia::render('Output/Feed/Show', [
            'feed' => $feed,
        ]);
    }

    public function store(Request $request, UserPermission $userPermission): RedirectResponse
    {
        $feed = $this->createFeedUseCase->execute(
            FeedCreateData::from($request->all()),
            $userPermission,
        );

        return redirect()->route('admin.output.feed.show', $feed->id)->with('success', 'Фид создан');
    }

    /**
     * Единственная точка входа обновления фида.
     *
     * Принимает только изменяемый параметр:
     *  - скалярные поля (name, setPreprice, setTitle, setDescription);
     *  - мутация списка (field + action add|remove|clear + in + ids).
     */
    public function update(Feed $feed, Request $request, UserPermission $userPermission): RedirectResponse
    {
        $payload = $request->only(['name', 'setPreprice', 'active', 'setTitle', 'setDescription', 'field', 'action', 'in']);
        $payload['ids'] = $this->resolveIds($request);

        $this->updateFeedUseCase->execute(
            $feed->id,
            FeedUpdateData::from($payload),
            $userPermission,
        );

        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(Feed $feed, UserPermission $userPermission): RedirectResponse
    {
        $this->removeFeedUseCase->execute($feed->id, $userPermission);

        return redirect()->back()->with('success', 'Фид удален');
    }

    /**
     * Идентификаторы могут приходить как ids (массив), product_id (один товар)
     * или products (массив товаров из пакетной загрузки).
     *
     * @return int[]
     */
    private function resolveIds(Request $request): array
    {
        if ($request->has('ids')) {
            return array_map('intval', (array) $request->input('ids'));
        }

        if ($request->filled('product_id')) {
            return [$request->integer('product_id')];
        }

        if ($request->has('products')) {
            return collect($request->input('products'))
                ->pluck('product_id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        return [];
    }
}
