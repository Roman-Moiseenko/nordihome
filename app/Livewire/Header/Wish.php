<?php

namespace App\Livewire\Header;

use App\Modules\Analytics\Domain\ValueObjects\ActionType;
use App\Modules\Analytics\Domain\ValueObjects\EntityType;
use App\Modules\Analytics\Presentation\Support\RecordsAnalyticsAction;
use App\Modules\Auth\Infrastructure\Models\Client;
use App\Modules\Cabinet\Application\Actions\Wish\RemoveWishUseCase;
use App\Modules\Cabinet\Application\Queries\ListWishClientQuery;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Collection;

class Wish extends Component
{
    use RecordsAnalyticsAction;

    public ?Client $client;
    public int $count = 0;
    public array $items = [];
    private  ListWishClientQuery $listWishClientQuery;
    private RemoveWishUseCase $removeWishUseCase;

    public function boot(
        ListWishClientQuery $listWishClientQuery,
        RemoveWishUseCase $removeWishUseCase,
    ): void
    {
        $this->listWishClientQuery = $listWishClientQuery;
        $this->removeWishUseCase = $removeWishUseCase;
        $this->client = (auth()->check() && auth()->user()->isClient()) ? auth()->user()->profileable : null;
    }

    public function mount(): void
    {

        $this->refresh_fields();
    }

    #[On('update-header-wish')]
    public function refresh_fields(): void
    {
        if (is_null($this->client)) return;

        $items = $this->listWishClientQuery->execute($this->client->id);
        $this->items = Collection::make($items)->toArray();

        $this->count = count($this->items);

    }

    public function remove($id): void
    {
        $productId = $this->removeWishUseCase->execute($id);

        $this->recordAnalyticsAction(
            ActionType::WISHLIST_REMOVE,
            EntityType::PRODUCT,
            (int)$productId,
        );

        $this->dispatch('update-wish', product_id: $productId);
        $this->refresh_fields();

    }

    public function remove_all()
    {

        foreach ($this->client->wishes as $wish) {
            $wish->delete();
        }
        $this->recordAnalyticsAction(ActionType::WISHLIST_CLEAR);

        $this->items = [];
        $this->count = 0;
        $this->dispatch('update-wish');
    }

    public function render()
    {
        return view('livewire.header.wish');

    }
}
