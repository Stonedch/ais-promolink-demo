@use('\App\Models\Event')

<table class="table m-0">
    <thead>
        <tr>
            <th scope="col" data-sort-field="name">
                <span>Название отчета</span>
                <button class="table__sort bg-transparent border-0" data-sort-field="name">
                    <img src="/img/sort.svg" width="14px">
                </button>
            </th>
            <th scope="col" data-sort-field="deadline">
                <span>Тип отчета</span>
                <button class="table__sort bg-transparent border-0" data-sort-field="category_name">
                    <img src="/img/sort.svg" width="14px">
                </button>
            </th>
            <th scope="col" data-sort-field="deadline">
                <span>Тип учреждений</span>
                <button class="table__sort bg-transparent border-0" data-sort-field="departament_type">
                    <img src="/img/sort.svg" width="14px">
                </button>
            </th>
            <th scope="col">
                <span>Взаимодействия</span>
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($forms as $form)
            <tr>
                <td class="align-middle" data-sort-field="name">
                    <b>{{ $form->name }}</b>
                </td>
                <td class="align-middle" data-sort-field="category_name">
                    {{ $form->form_category_name }}
                </td>
                <td class="align-middle" data-sort-field="departament_type">
                    {{ implode(', ', $form->departament_types->pluck('name')->toArray()) }}
                </td>
                <td class="align-middle">
                    <div class="btn-group" role="group">
                        <a href="{{ route('web.minister.by-form', ['form' => $form->id]) }}"
                            type="button" class="btn btn-secondary py-1 px-3">
                            <img width="16px" src="/img/search.svg" />
                            <small>просмотр</small>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
