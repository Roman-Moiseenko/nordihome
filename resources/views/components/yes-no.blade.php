<div class="truncate sm:whitespace-normal flex items-center {{ $class }}">
    @if(!empty($lucide))
    <x-base.lucide icon="{{ $lucide }}" class="w-4 h-4 mr-2"/>
    @endif
    {{ $title }}
    @if($status)
    <span class="ml-1 rounded bg-success/20 text-success px-2 ">Да</span>
    @else
    <span class="ml-1 rounded bg-danger/20 text-danger px-2 ">Нет</span>
    @endif
</div>
