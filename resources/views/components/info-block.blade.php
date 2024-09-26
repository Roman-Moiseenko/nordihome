<div id="{{ $id }}" class="box p-3 mt-3 accordion">
    <div class="rounded-md border border-slate-200/60 p-3 dark:border-darkmode-400 accordion-item" style="margin-bottom: 0 !important; margin-top: 0 !important;">
        <div class="flex items-center border-b border-slate-200/60 pb-3 text-base font-medium accordion-header">
            <button class="accordion-button @if(!$show) collapsed @endif flex" type="button" data-tw-toggle="collapse"
                    data-tw-target="#faq-accordion-collapse-{{ $id }}"
                    aria-expanded="true"
                    aria-controls="faq-accordion-collapse-{{ $id }}">
                <x-base.lucide class="mr-2 h-4 w-4 dropdown-block" icon="ChevronDown"/> {{ $title }}
            </button>
            @if(!empty($route))
                <button class="btn btn-primary shadow-md ml-3"
                        onclick="window.location.href='{{ $route }}'">
                    <x-base.lucide class="h-4 w-4" icon="pencil"/>
                </button>
            @endif
        </div>
        <div id="faq-accordion-collapse-{{ $id }}" class="accordion-collapse collapse @if($show) show @endif">
            {{ $slot }}
        </div>
    </div>
    @once
        @push('scripts')
            @vite("resources/js/vendor/accordion/index.js")
        @endpush
    @endonce

</div>

