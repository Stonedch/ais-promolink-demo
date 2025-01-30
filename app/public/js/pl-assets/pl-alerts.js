export class PLAlert {
    static TYPE_DANGER = "alert-danger";
    static TYPE_PRIMARY = "alert-primary";
    static TYPE_SUCCESS = "alert-success";

    #selector;
    #alerts;

    constructor(alertSelector = ".alert") {
        this.#selector = alertSelector;
    }

    initialCloseButtonEvents(closeButtonSelector = ".close-button") {
        _.forEach(this.#getAlerts(this.#selector), function (alert) {
            const closeButtons = alert.querySelectorAll(closeButtonSelector);

            _.forEach(closeButtons, function (closeButton) {
                closeButton.addEventListener("click", function () {
                    alert.style.opacity = 1;
                    alert.remove();
                });
            });
        });

        return this;
    }

    #getAlerts() {
        return [...document.querySelectorAll(this.#selector)];
    }

    show(title, body = "", type = PLAlert.TYPE_PRIMARY) {
        const position = "beforeend";

        const text = `
            <div class="alert ${type} w-25 opacity-75 position-fixed bottom-0 start-50 translate-middle-x" role="alert">
                <h4 class="alert-heading">${title}</h4>
                <div>${body}</div>
                <button class="close-button position-absolute top-0 start-100 translate-middle badge border border-light rounded-circle bg-danger p-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"></path>
                    </svg>
                </button>
            </div>
        `;

        document.querySelector("body").insertAdjacentHTML(position, text);

        this.initialCloseButtonEvents();

        return this;
    }
}