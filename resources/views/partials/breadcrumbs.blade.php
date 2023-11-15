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
                    <x-base.breadcrumb.link :index="$index" :href="$breadcrumb->url">{!! $breadcrumb->title !!}</x-base.breadcrumb.link>
                @else
                    <x-base.breadcrumb.link :index="$index" :active="true">{{ $breadcrumb->title }}</x-base.breadcrumb.link>
                @endif
            @endforeach
    </x-base.breadcrumb>
@endunless
