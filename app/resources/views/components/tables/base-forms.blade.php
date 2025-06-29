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
                <button class="table__sort bg-transparent border-0" data-sort-field="deadline">
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
                    @if ($form->deadline)
                        {{ $form->deadline }} дней
                    @else
                        бессрочно
                    @endif
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
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<script>
    $(document).ready(function () {
        $("[data-action=\"by-initiative\"]").click(function() {
            const formIdentifier = $(this).data("id");

            Fancybox.close();

            new Fancybox([{
                src: `
                    <form class="by-initiative-form" id="byInitiativeForm">
                        <div class="form-group mb-3">
                            <label>Дата заполнения отчета</label>
                            <input type="date" class="form-control" name="filled_at">
                            <small id="emailHelp" class="form-text text-muted">Дата заполнения, которая будет выставлена после утверждения отчета</small>
                        </div>
                        <button type="submit" class="btn btn-primary">Приступить к заполнению</button>
                    </form>
                `,
                type: "html",
            }], {
                on: {
                    done: () => {
                        $("form.by-initiative-form").on("submit", function(event) {
                            event.preventDefault();
                            Fancybox.close();
                            var request = form2obj("#byInitiativeForm");
                            request['identifier'] = formIdentifier;
                            $.post("/api/forms/by-initiative", request, function (response) {
                                window.location.replace(response.data.url);
                            });
                        });
                    }
                }
            });
        });
    });
</script>
