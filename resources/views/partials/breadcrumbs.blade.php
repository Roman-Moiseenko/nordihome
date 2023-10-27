@unless ($breadcrumbs->isEmpty())
    <x-base.breadcrumb
        @class([
            'h-[45px] md:ml-10 md:border-l border-white/[0.08] dark:border-white/[0.08] mr-auto -intro-x',

            'md:pl-10' => true,
        ])
        light>
        <!--ol class="breadcrumb breadcrumb-light"-->
            @foreach ($breadcrumbs as $index => $breadcrumb)

                @if ($breadcrumb->url && !$loop->last)
                    <x-base.breadcrumb.link :index="$index" :href="$breadcrumb->url">{{ $breadcrumb->title }}</x-base.breadcrumb.link>
                    <!-- li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li -->
                @else
                    <x-base.breadcrumb.link :index="$index" :active="true">{{ $breadcrumb->title }}</x-base.breadcrumb.link>
                    <!--li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li-->
                @endif
            @endforeach
    </x-base.breadcrumb>
        <!--/ol-->
@endunless
