<div class="widget-variants {{ $class }}">
    <div class="accordion" id="attribute-{{ $id }}">
        <div class="accordion-item">
            <div class="accordion-header widget-name" id="headingOne-{{ $id }}">
                <button class="accordion-button collapsed"
                        type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne-{{ $id }}" aria-expanded="false"
                        aria-controls="collapseOne-{{ $id }}">
                    {{ $caption }}
                </button>
            </div>
            <!--span class="widget-name">{{ $caption }}</span-->
            <div id="collapseOne-{{ $id }}" class="accordion-collapse collapse mt-1" aria-labelledby="headingOne-{{ $id }}"
                 data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>

@once
    @push('scripts')
        @vite('resources/js/components/widget.js')
    @endpush
@endonce
