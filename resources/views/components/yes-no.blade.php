<div class="truncate sm:whitespace-normal flex items-center {{ $class }}">
    @if(!empty($lucide))
    <x-base.lucide icon="{{ $lucide }}" class="w-4 h-4 mr-2"/>
    @endif
    {{ $title }}
    @if($status)
        <span class="rounded bg-success/20 text-success px-2 mx-auto">Да</span>
    @else
        <span class="rounded bg-danger/20 text-danger px-2 mx-auto">Нет</span>
    @endif
</div>
