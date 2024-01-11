<div class="grid grid-cols-12 gap-4 p-3 border-b">
    <div class="col-span-12 lg:col-span-3">
        @if($item->type == \App\Modules\Admin\Entity\SettingItem::KEY_BOOL)
            {{ \App\Forms\CheckSwitch::create($item->key, [
             'placeholder' => $item->name,
             'value' => $item->value,
             'class' => 'ml-3',
             ])->show() }}

        @elseif($item->type == \App\Modules\Admin\Entity\SettingItem::KEY_INTEGER)
            {{ \App\Forms\Input::create($item->key,
                ['placeholder' => $item->name, 'class' => 'ml-0 w-full lg:w-52', 'value' => $item->value])->
                label($item->name)->type('number')->show() }}

        @else
            {{ \App\Forms\Input::create($item->key,
                ['placeholder' => $item->name, 'class' => 'ml-0 w-full lg:w-52', 'value' => $item->value])->
                label($item->name)->show() }}
        @endif
    </div>
    <div class="col-span-12 lg:col-span-3 flex items-center">
                <span class="text-primary">
                {{ $item->description }}
                </span>
    </div>
</div>
