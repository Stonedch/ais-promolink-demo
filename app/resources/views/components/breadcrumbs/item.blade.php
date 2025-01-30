<li class="breadcrumb-item{{ @$current ? ' active' : '' }}"{!! @$current ? ' aria-current="page"' : '' !!}>
    @if (empty(@$current))
        <a href="{{ $href }}">{{ $slot }}</a>
    @else
        {{ $slot }}
    @endif
</li>
