
@if ($paginator->hasPages())
<div class="col-span-12 flex flex-wrap sm:flex-row sm:flex-nowrap items-center mt-3">
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

    <select id="select-pagination" name="p" class="w-20 form-select box mt-3 sm:mt-0">
        @foreach(Illuminate\Support\Facades\Config::get((isset($card)) ? 'shop-config.options-card' : 'shop-config.options-list') as $value)
        <option value="{{ $value }}" {{ ($value == $pagination) ? 'selected' : ''}}>{{ $value }}</option>
        @endforeach
    </select>
    <script>
        const urlParams = new URLSearchParams(window.location.search);
        let select = document.getElementById('select-pagination');
        select.addEventListener('change', function () {
            let p = select.options[select.selectedIndex].value;
            urlParams.set('p', p);
            window.location.search = urlParams;
        });
    </script>
</div>

@endif
