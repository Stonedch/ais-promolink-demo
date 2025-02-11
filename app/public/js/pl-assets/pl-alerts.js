export class PLAlert {
    static TYPE_DANGER = "#dc3545";
    static TYPE_PRIMARY = "#007aff";
    static TYPE_SUCCESS = "#198754";

    #selector;

    constructor(alertSelector = ".toast") {
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

        const text = /* HTML */`
            <div class="toast d-block position-fixed bottom-0 start-50 translate-middle-x m-1" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <svg class="bd-placeholder-img rounded me-2"
                        width="20" height="20"
                        xmlns="http://www.w3.org/2000/svg"
                        aria-hidden="true"
                        preserveAspectRatio="xMidYMid slice" focusable="false">
                        <rect width="100%" height="100%" fill="${type}"></rect>
                    </svg>
                    <strong class="me-auto">${title}</strong>
                    <button type="button" class="btn-close close-button" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    ${body}
                </div>
            </div>
        `;

        document.querySelector("body").insertAdjacentHTML(position, text);

        this.initialCloseButtonEvents();

        return this;
    }
}