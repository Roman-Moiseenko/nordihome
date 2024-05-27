<div>

    <div class="d-flex">
        <div class="rating-area @if($edit) edit @endif">
            <input type="radio" id="star-5" name="rating" value="5" wire:model="rating" @if(!$edit) disabled
                   readonly @endif>
            <label for="star-5" title="Оценка «5»"></label>
            <input type="radio" id="star-4" name="rating" value="4" wire:model="rating" @if(!$edit) disabled
                   readonly @endif>
            <label for="star-4" title="Оценка «4»"></label>
            <input type="radio" id="star-3" name="rating" value="3" wire:model="rating" @if(!$edit) disabled
                   readonly @endif>
            <label for="star-3" title="Оценка «3»"></label>
            <input type="radio" id="star-2" name="rating" value="2" wire:model="rating" @if(!$edit) disabled
                   readonly @endif>
            <label for="star-2" title="Оценка «2»"></label>
            <input type="radio" id="star-1" name="rating" value="1" wire:model="rating" @if(!$edit) disabled
                   readonly @endif>
            <label for="star-1" title="Оценка «1»"></label>
        </div>
        <div class="ms-auto">
            <div class="fs-7">
                {{ $review->htmlDate() }}
            </div>
            <div class="badge
                    @if($review->isDraft()) text-bg-secondary @endif
            @if($review->isModerated()) text-bg-warning @endif
            @if($review->isPublished()) text-bg-success @endif
            @if($review->isBlocked()) text-bg-danger @endif
                ">{{ $review->statusHtml() }}</div>
        </div>

    </div>
    <div class="mt-3">
        <textarea @if(!$edit) disabled readonly @endif wire:model="text" rows="5"
                  class="form-control @if(!$edit) no-resize @endif "></textarea>
    </div>
    <div class="mt-3">
        @if(!is_null($review->photo))
            Фотография
        @endif
        @if($edit && is_null($review->photo))
            <div
                x-data="{ uploading: false, progress: 0 }"
                x-on:livewire-upload-start="uploading = true"
                x-on:livewire-upload-finish="uploading = false"
                x-on:livewire-upload-cancel="uploading = false"
                x-on:livewire-upload-error="uploading = false"
                x-on:livewire-upload-progress="progress = $event.detail.progress"
                style="width: 256px;"
            >

                <input type="file" id="exampleInputName" wire:model="image" placeholder="">

                <div x-show="uploading">
                    <progress max="100" x-bind:value="progress"></progress>
                </div>
            </div>
            @if ($image)
                <img src="{{ $image->temporaryUrl() }}" style="width: 100px; height: 100px;" class="mt-2">
            @endif
        @endif
    </div>

    <div class="mt-3">
        <button class="btn btn-primary" wire:click="toggle_button">{{ $caption }}</button>
        @if($edit)
            <button class="btn btn-secondary ms-2" wire:click="cancel_button">Отменить</button>
        @endif
    </div>
</div>
