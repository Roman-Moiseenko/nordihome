
@if ($paginator->hasPages())
<div class="intro-y col-span-12 flex flex-wrap sm:flex-row sm:flex-nowrap items-center">
    <nav class="w-full sm:w-auto sm:mr-auto">
        <ul class="pagination">
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">
                        <i data-lucide="chevrons-left" width="24" height="24" class="lucide lucide-chevrons-left w-4 h-4"></i>
                    </span>
                </li>
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link"><i data-lucide="chevron-left" width="24" height="24" class="lucide lucide-chevrons-left w-4 h-4"></i></span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->url(1) }}">
                        <i data-lucide="chevrons-left" width="24" height="24" class="lucide lucide-chevrons-left w-4 h-4"></i>
                    </a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}">
                        <i data-lucide="chevron-left" width="24" height="24" class="lucide lucide-chevrons-left w-4 h-4"></i>
                    </a>
                </li>
            @endif
                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                    @endif
                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                            @endif
                        @endforeach
                    @endif
                @endforeach
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}">
                            <i data-lucide="chevron-right" width="24" height="24" class="lucide lucide-chevrons-right w-4 h-4"></i>
                        </a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}">
                            <i data-lucide="chevrons-right" width="24" height="24" class="lucide lucide-chevrons-right w-4 h-4"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link" aria-hidden="true">
                            <i data-lucide="chevron-right" width="24" height="24" class="lucide lucide-chevrons-right w-4 h-4"></i>
                        </span>
                    </li>
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link" aria-hidden="true">
                            <i data-lucide="chevrons-right" width="24" height="24" class="lucide lucide-chevrons-right w-4 h-4"></i>
                        </span>
                    </li>
                @endif
        </ul>
    </nav>
</div>

@endif
