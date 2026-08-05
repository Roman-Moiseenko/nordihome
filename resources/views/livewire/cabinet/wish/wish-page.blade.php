<div>
    @foreach($wishes as $wish)
        <livewire:cabinet.wish.wish-item :wish="$wish" :key="$wish->id"/>
    @endforeach

    @if(count($wishes) == 0 )
        <div class="fs-5 m-3 mb-5">
            У вас нет товаров в избранном.
        </div>
    @endif
</div>
