@use('\App\Models\Event')

<table class="table m-0">
    <thead>
        <tr>
            <th scope="col">Название отчета</th>
            <th scope="col">Срок исполнения</th>
            <th scope="col">Статус</th>
            <th scope="col">Взаимодействия</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($forms as $form)
            <tr>
                <td class="align-middle">
                    <b>{{ $form->name }}</b>
                </td>
                <td class="align-middle">
                    {{ $form->deadline }} дней
                </td>
                <td class="align-middle">
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
