import { PLAlert } from "./pl-alerts.js";

export class PLCustomReport {
    #formSelector;
    #forms;

    constructor(formSelector = ".custom-report-form") {
        this.#formSelector = formSelector;
        this.#forms = this.#getForms();
    }

    initialSubmitButton() {
        _.forEach(this.#forms, (form) => {
            form.addEventListener("submit", (event) => {
                event.preventDefault();
                this.#submit(form, new FormData(form));
            })
        });
    }

    #getForms() {
        return [...document.querySelectorAll(this.#formSelector)];
    }

    async #submit(form, formData) {
        await fetch("/api/custom-reports/store", {
            method: "POST",
            body: formData,
        })
            .then(response => response.json())
            .then(response => {
                if (response.state == false) {
                    throw new Error(response.error);
                }

                form.reset();

                (new PLAlert()).show("Успешно!", "", PLAlert.TYPE_SUCCESS);

                return repsonse;
            })
            .catch(error => {
                (new PLAlert()).show("Ошибка!", error.message, PLAlert.TYPE_DANGER);
            });
    }
}
