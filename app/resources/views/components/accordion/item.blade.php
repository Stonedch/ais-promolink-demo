<div class="accordion-item">
    <h2 class="accordion-header" id="{{ $id }}-headingOne">
        <button class="accordion-button" type="button" data-bs-toggle="collapse"
            data-bs-target="#{{ $id }}-collapseOne" aria-expanded="true"
            aria-controls="{{ $id }}-collapseOne">
            {{ @$title }}
        </button>
    </h2>
    <div id="{{ $id }}-collapseOne" @class(['accordion-collapse', 'collapse', 'show' => $show])
        aria-labelledby="{{ $id }}-headingOne">
        <div class="accordion-body p-0">
            {{ $slot }}
        </div>
    </div>
</div>
