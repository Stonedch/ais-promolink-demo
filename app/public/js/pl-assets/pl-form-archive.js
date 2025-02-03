import { PLModal } from "./pl-modal.js";

export class PLFormArchive {
    constructor() {
    }

    initialShowEvent() {
        _.forEach([...document.querySelectorAll(".archive-open")], function (archiveButton) {
            archiveButton.addEventListener("click", (event) => {
                event.preventDefault();

                const eventIdentifier = archiveButton.dataset.event;

                fetch(`/api/forms/archive?event=${eventIdentifier}`)
                    .then(response => response.json())
                    .then(response => {
                        var rows = "";

                        _.each(response.data.events, (event) => {
                            rows += /* HTML */ `
                                <tr>
                                    <td class="w-50">${event.filled_at}</td>
                                    <td class="w-50">
                                        <a href="/forms/preview/${event.departament_id}/${event.form_id}?event=${event.id}"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="bottom"
                                            title="Просмотр">
                                            <img class="_icon-link" width="20px" src="/img/search.svg" />
                                        </a>
                                    </td>
                                </tr>
                            `;
                        });

                        PLModal.show(/* HTML */`
                            <table class="table m-0 w-auto">
                                <thead>
                                    <tr>
                                        <th scope="col">Дата заполнения</th>
                                        <th scope="col">Взаимодействия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${rows}
                                </tbody>
                            </table>
                        `);
                    });
            });
        });
    }
}