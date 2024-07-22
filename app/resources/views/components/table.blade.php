@props(['headers', 'data', 'alphabetical' => false, 'sort' => true])

@php
    $id = uniqid();
    $sortDirections = array_fill(0, count($headers), 'asc');
@endphp

<table class="table mt-1" id="table-{{ $id }}">
    <thead class="rounded">
        <tr>
            @foreach ($headers as $index => $header)
                <th
                    @if ($loop->first) class="text-start" @elseif ($loop->last) class="text-end" @endif>
                    <div class="d-flex align-items-center gap-1 width-max"
                        @if ($loop->last && !$loop->first) style="width:max-content; margin-left:auto" @endif>
                        {{ $header }}
                        @if ($sort && $index > 0)
                            <span class="sort-icon table__sort table__sort--minister" data-index="{{ $index }}"
                                data-direction="asc" style="cursor: pointer;"></span>
                        @endif
                    </div>
                </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @php $prevFirstLetter = ''; @endphp
        @foreach ($data as $row)
            @if ($alphabetical == true)
                @php
                    $firstLetter = mb_substr($row['cells'][0], 0, 1);
                @endphp
                @if ($alphabetical && isset($row['cells'][0]))
                    @if ($firstLetter != $prevFirstLetter)
                        <tr>
                            <td colspan="{{ count($headers) }}" style="color: var(--main-color)">
                                <strong>{{ $firstLetter }}</strong>
                            </td>
                        </tr>
                        @php $prevFirstLetter = $firstLetter; @endphp
                    @endif
                @endif
            @endif
            <tr class="border-bottom">
                @foreach ($row['cells'] as $key => $value)
                    <td>
                        <a href="{{ $row['link'] }}"
                            @if ($loop->first) class="text-start" @elseif ($loop->last) class="text-end" @endif>
                            <span>{{ $value }}</span>
                        </a>
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

<script>
    function initCountersToTable() {
        $.each($(".accordion-body .table tbody"), function() {
            var counter = 0;

            $.each($(this).find("tr.border-bottom"), function() {
                var selector = $(this).find("td:first-child span");
                $(this).find("td:first-child .counter").remove();
                var title = $(this).find("td:first-child").text();
                title = `<span class="counter">${++counter}.</span> ${title}`;
                selector.html(title);
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const table = document.querySelector('#table-{{ $id }}');
        const sortIcons = table.querySelectorAll('.sort-icon');

        initCountersToTable();

        sortIcons.forEach(icon => {
            icon.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-index'));
                const direction = this.getAttribute('data-direction');
                const newDirection = direction === 'asc' ? 'desc' : 'asc';

                let rowsArray = Array.from(table.querySelectorAll('tbody tr'));
                rowsArray = rowsArray.filter(row => !row.querySelector('td[colspan] strong'));

                rowsArray.sort((rowA, rowB) => {
                    const cellA = rowA.querySelectorAll('td')[index].innerText.trim();
                    const cellB = rowB.querySelectorAll('td')[index].innerText.trim();

                    if (newDirection === 'asc') {
                        return cellA.localeCompare(cellB, undefined, {
                            numeric: true
                        });
                    } else {
                        return cellB.localeCompare(cellA, undefined, {
                            numeric: true
                        });
                    }
                });

                const tbody = table.querySelector('tbody');
                tbody.innerHTML = '';
                rowsArray.forEach(row => tbody.appendChild(row));

                this.setAttribute('data-direction', newDirection);

                initCountersToTable();
            });
        });
    });
</script>
