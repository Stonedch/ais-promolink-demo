@props(['title', 'id'])

@php
    $id = uniqid();
@endphp

<div class="accordion-item">
    <h2 class="accordion-header" id="heading{{ $id }}">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapse{{ $id }}" aria-expanded="false" aria-controls="collapse{{ $id }}">
            {{ $title }}
        </button>
    </h2>
    <div id="collapse{{ $id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $id }}"
         data-bs-parent="#accordionExample">
        <div class="accordion-body">
            {{ $slot }}
        </div>
    </div>
</div>
