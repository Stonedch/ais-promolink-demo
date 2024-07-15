@props(['headers', 'data', 'alphabetical' => false])

@php
    $id = uniqid()
@endphp

<table class="table mt-1" id="table-{{$id}}">
    <thead class="rounded">
    <tr>
        @foreach ($headers as $header)
            <th @if ($loop->first) class="text-start" @elseif ($loop->last) class="text-end" @endif>{{ $header }}</th>
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
                    <a href="{{ $row['link']  }}"
                       @if ($loop->first) class="text-start" @elseif ($loop->last) class="text-end" @endif>
                        <span>{{ $value}} </span>
                    </a>
                </td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelector(`#table-{{$id}}`).addEventListener('click', () => {
            // TODO
        })
    })
</script>
