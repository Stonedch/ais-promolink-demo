@use('\App\Models\Event')

<table class="table m-0">
    <thead>
        <tr>
            <th scope="col" class="w-50">
                <span>Тип</span>
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
        @foreach ($types as $type)
            <tr>
                <td>{{ $type->name }}</td>
                <td>{{ $type->count }}</td>
                <td>
                    <div class="btn-group" role="group">
                        <a href="{{ route('web.minister.by-departament-type', ['departamentType' => $type->id]) }}" type="button"
                            class="btn btn-secondary py-1 px-3">
                            <small>Перейти</small>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
