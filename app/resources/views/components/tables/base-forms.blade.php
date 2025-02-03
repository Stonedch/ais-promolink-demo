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
                <span>Срок исполнения</span>
                <button class="table__sort bg-transparent border-0" data-sort-field="sort">
                    <img src="/img/sort.svg" width="14px">
                </button>
            </th>
            <th scope="col" data-sort-field="status">
                <span>Статус</span>
                <button class="table__sort bg-transparent border-0" data-sort-field="status">
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
                <td class="align-middle" data-sort-field="deadline">
                    {{ $form->deadline }} дней
                </td>
                <td class="align-middle" data-sort-field="status">
                    {!! $form->event->getCurrentStatus()->bootstrapme() !!}
                </td>
                <td class="align-middle">
                    <div class="btn-group" role="group">
                        @if ($form->canUserEdit(auth()->user()))
                            <a href="{{ route('web.forms.index', ['id' => $form->event->id]) }}" type="button"
                                class="btn btn-secondary py-1 px-3">
                                <img width="16px" src="/img/pencil-square.svg" />
                                <small>редактировать</small>
                            </a>
                        @endif
                        {{-- <button type="button" class="btn btn-secondary py-1 px-3">
                            <img width="16px" src="/img/files.svg" />
                            <small>архив</small>
                        </button> --}}
                        <a href="/forms/preview/{{ $form->event->departament_id }}/{{ $form->id }}?event={{ $form->event->id }}"
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
