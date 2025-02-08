@unless ($breadcrumbs->isEmpty())
    <nav
        aria-label="breadcrumb" class="breadcrumbs">
        <ol class="breadcrumbs">
            @foreach ($breadcrumbs as $index => $breadcrumb)
                @if ($breadcrumb->url && !$loop->last)
                    <li class="breadcrumb-item" data-index="{{ $index }}"><a
                            href="{{ $breadcrumb->url }}">{!! $breadcrumb->title !!}  </a></li>
                @else
                    <li class="breadcrumb-item active hide-mobile" data-index="{{ $index }}"><span>{!! $breadcrumb->title !!}</span>
                    </li>
                @endif
            @endforeach
        </ol>
    </nav>
    {!! isset($schema) ? $schema->BreadCrumbs($breadcrumbs) : '' !!}
@endunless
