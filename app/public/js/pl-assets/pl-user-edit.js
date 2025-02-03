import { PLModal } from "./pl-modal.js";

export class PLUserEdit {
    #selector;

    constructor(selector = ".user-edit") {
        this.#selector = selector;
    }

    initialShowEvent() {
        _.forEach([...document.querySelectorAll(this.#selector)], (button) => {
            button.addEventListener("click", function () {
                PLModal.show(/* HTML */ `
                    <div class="bg-light">
                        <div class="container mb-4 bg-light p-3 rounded">
                            <h3>Редактирование профиля</h3>
                            <form id="userEditForm" class="custom-report-form">
                                <div class="row mb-3">
                                    <div class="mb-3">
                                        <label for="avatar[attachment]" class="form-label">Изображение профиля</label>
                                        <input class="form-control" type="file" name="avatar[attachment]">
                                    </div>
                                    <div class="">
                                        <button type="submit" class="submit-button btn btn-primary mt-auto w-100">Отправить</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                `, () => {
                    _.forEach([...document.querySelectorAll("#userEditForm")], (form) => {
                        form.addEventListener("submit", (event) => {
                            event.preventDefault();
                            const formData = new FormData(form);

                            fetch("/api/user-avatar", {
                                method: "POST",
                                body: formData,
                            })
                                .then(response => response.json())
                                .then(response => {
                                    if (response.state == false) {
                                        throw new Error(response.error);
                                    }

                                    form.reset();
                                    window.location.reload();
                                    return repsonse;
                                })
                                .catch(error => {
                                    (new PLAlert()).show("Ошибка!", error.message, PLAlert.TYPE_DANGER);
                                });
                        });
                    });
                });
            });
        });
    }
}