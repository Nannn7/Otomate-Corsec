@unless ($breadcrumbs->isEmpty())
    <div class="flex [.header_&amp;]:below-lg:hidden items-center gap-1.25 text-xs lg:text-sm font-medium mb-2.5 lg:mb-0" data-reparent="true" data-reparent-mode="prepend|lg:prepend" data-reparent-target="#content_container|lg:#header_container">
        @foreach ($breadcrumbs as $breadcrumb)
            @if (!$loop->last)
                @if(!is_null($breadcrumb->url))
                    <span class="text-gray-600">
                        <a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a>
                    </span>
                @else
                    <span class="text-gray-700">
                        {{ $breadcrumb->title }}
                    </span>
                @endif
                <i class="ki-filled ki-right text-gray-500 text-3xs">
                </i>
            @else
                <span class="text-gray-700">
                    {{ $breadcrumb->title }}
                </span>
            @endif

        @endforeach
    </div>
@endunless
