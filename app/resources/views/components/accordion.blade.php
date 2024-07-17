@props(['title', 'id', 'open' => true])


@php
    $id = uniqid();
@endphp

<div class="accordion-item">
    <h2 class="accordion-header" id="heading{{ $id }}">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapse{{ $id }}" aria-expanded="false" aria-controls="collapse{{ $id }}">
            {{ $title }}
            <label class="accordion-search-input">
                <input type="text" id="searchInput-{{ $id }}" placeholder="Поиск">
            </label>
        </button>
    </h2>
    <div id="collapse{{ $id }}" class="accordion-collapse collapse {{$open === true ? 'show' : ''}}" aria-labelledby="heading{{ $id }}"
         data-bs-parent="#accordionExample">
        <div class="accordion-body" id="accordion-body-{{ $id }}">
            {{ $slot }}
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const table = document.querySelector("#accordion-body-{{ $id }}").querySelector('.table')
        const input = document.querySelector("#searchInput-{{ $id }}")
        input.addEventListener('input', () => {
            document.querySelector('#collapse{{ $id }}').classList.add('show')

            const searchString = input.value.trim().toLowerCase();
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const isAlphabetRow = row.querySelector('td[colspan] strong');
                if (isAlphabetRow) {
                    if (input.value !== '') {
                        row.style.display = 'none'
                    } else {
                        row.style.display = ''
                    }
                }

                const firstCell = row.querySelector('td:first-child a');
                if (firstCell) {
                    const firstCellText = firstCell.textContent.trim().toLowerCase();
                    if (firstCellText.includes(searchString)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        })
    });
</script>
