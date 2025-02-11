@use('\App\Models\Event')

<table class="table m-0">
    <thead>
        <tr>
            <th scope="col" class="w-50">
                <span>Район</span>
            </th>
            <th scope="col" class="w-25">
                <span>Количество</span>
            </th>
            <th scope="col" class="w-25">
                <span>Взаимодействия</span>
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($districts as $letter => $slice)
            <tr>
                <td>
                    <b class="text-primary">{{ $letter }}</b>
                <td>
                <td></td>
                <td></td>
            </tr>
            @foreach ($slice as $district)
                <tr>
                    <td>{{ $district->name }}</td>
                    <td>{{ $district->count }}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ route('web.minister.by-district', ['district' => $district->id]) }}"
                                type="button" class="btn btn-secondary py-1 px-3">
                                <small>Перейти</small>
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
