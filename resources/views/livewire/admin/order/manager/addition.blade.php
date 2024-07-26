<div>
    <div class="box flex items-center p-2">
        <div class="w-20 text-center">{{ $i + 1 }}</div>
        <div class="w-56 text-center">{{ $addition->purposeHTML() }}</div>
        <div class="w-40 input-group">
            <input type="number" class="form-control text-right" aria-describedby="addition-amount"
                   min="0" autocomplete="off"
                   wire:change="set_amount" wire:model="amount" wire:loading.attr="disabled"
            >
            <div id="addition-amount" class="input-group-text">₽</div>
        </div>

        <div class="w-56 text-center ml-2">
            <input type="text" class="form-control text-right" autocomplete="off"
                   wire:change="set_comment" wire:model="comment" wire:loading.attr="disabled"
            >
        </div>
        <div class="w-20 text-center">
            <button class="btn btn-outline-danger ml-6" type="button"
                    wire:click="delete"
                    wire:confirm="Удалить позицию из заказа?"
            >X</button>

        </div>
    </div>
</div>
