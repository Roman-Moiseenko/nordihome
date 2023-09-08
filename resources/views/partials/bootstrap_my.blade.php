@unless ($breadcrumbs->isEmpty())
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            @foreach ($breadcrumbs as $breadcrumb)

                @if ($breadcrumb->url && !$loop->last)
                    <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
                @else
                    <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
                @endif

            @endforeach
        </ul>
    </nav>
@endunless
