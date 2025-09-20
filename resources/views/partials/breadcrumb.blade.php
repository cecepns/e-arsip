{{--
    Reusable Breadcrumb Component
    Params:
    - items: array of ['label' => string, 'url' => string|null]
    Usage:
    @include('partials.breadcrumb', [
        'items' => [
            ['label' => 'Home', 'url' => '/'],
            ['label' => 'Dashboard']
        ]
    ])
--}}
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        @foreach($items as $item)
            @if(!empty($item['url']))
                <li class="breadcrumb-item"><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
            @else
                <li class="breadcrumb-item active">{{ $item['label'] }}</li>
            @endif
        @endforeach
    </ol>
</nav>
