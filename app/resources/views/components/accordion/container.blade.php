<div class="container mb-5">
    <div class="accordion">
        @if (@$title)
            <h3 class="mb-3">{{ $title }}</h3>
        @endif
        {{ $slot }}
    </div>
</div>
