@use('\App\Models\Event')

<table class="table m-0">
    <thead>
        <tr>
            <th scope="col" class="w-50">
                <span></span>
            </th>
            <th scope="col" class="w-25">
                <span>Рейтинг</span>
            </th>
            <th scope="col" class="w-25">
                <span>Взаимодействия</span>
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($departaments as $departament)
            <tr>
                <td>{{ $departament->name }}</td>
                <td>{{ $departament->rating }}</td>
                <td>
                    <div class="btn-group" role="group">
                        <a href="{{ route('web.minister.by-district', ['district' => $district->id, 'departament' => $departament->id]) }}"
                            type="button" class="btn btn-secondary py-1 px-3">
                            <small>Перейти</small>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
