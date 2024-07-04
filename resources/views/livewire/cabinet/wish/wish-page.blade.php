<div>
    @foreach($user->wishes as $wish)
        <livewire:cabinet.wish.wish-item :wish="$wish" :key="$wish->id" :user="$user"/>
    @endforeach

    @if($user->wishes()->count() == 0 )
        <div class="fs-5 m-3 mb-5">
            У вас нет товаров в избранном.
        </div>
    @endif
</div>
