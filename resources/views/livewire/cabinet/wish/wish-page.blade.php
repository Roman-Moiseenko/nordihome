<div>
    @foreach($client->wishes as $wish)
        <livewire:cabinet.wish.wish-item :wish="$wish" :key="$wish->id" :client="$client"/>
    @endforeach

    @if($client->wishes()->count() == 0 )
        <div class="fs-5 m-3 mb-5">
            У вас нет товаров в избранном.
        </div>
    @endif
</div>
