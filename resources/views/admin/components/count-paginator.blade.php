<div class="hidden md:block mx-auto text-slate-500">
    @if ($paginator->hasPages())
    Показано с<span class="fw-semibold"> {{ $paginator->firstItem() }} </span>по<span class="fw-semibold"> {{ $paginator->lastItem() }} </span>из<span class="fw-semibold"> {{ $paginator->total() }} </span>элементов
    @endif
</div>
