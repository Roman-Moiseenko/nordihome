@unless ($breadcrumbs->isEmpty())
    <nav
        style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);"
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
