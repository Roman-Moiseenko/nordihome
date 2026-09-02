<div>
    <div class="box-card d-flex wish-item">
        <div class="wish-item-img">
            <a href="{{ $wish['url'] }}" target="_blank"><img
                    src="{{ $wish['image'] }}"/></a>
        </div>
        <div class="wish-item-info">
            <a href="{{ $wish['url'] }}" target="_blank">{{ $wish['name'] }}</a>
        </div>
        <div class="wish-item-control">
            <button class="btn btn-light" title="Удалить из избранного"
                    wire:click="remove">
                <i class="fa-light fa-trash-can"></i>
            </button>
        </div>
    </div>
</div>
