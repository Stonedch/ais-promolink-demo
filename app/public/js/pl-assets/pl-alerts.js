export class PLAlert {
    #selector;
    #alerts;

    constructor(alertSelector = ".alert") {
        this.#selector = alertSelector;
        this.#alerts = this.#getAlerts(this.#selector);
    }

    initialCloseButtonEvents(closeButtonSelector = ".close-button") {
        _.forEach(this.#alerts, function (alert) {
            const closeButtons = alert.querySelectorAll(closeButtonSelector);

            _.forEach(closeButtons, function (closeButton) {
                closeButton.addEventListener("click", function () {
                    alert.style.opacity = 1;

                    (function fade() {
                        if ((alert.style.opacity -= .1) < 0) {
                            alert.style.display = "none";
                            alert.remove();
                        } else {
                            requestAnimationFrame(fade);
                        }
                    })();
                });
            });
        });

        return this;
    }

    #getAlerts() {
        return [...document.querySelectorAll(this.#selector)];
    }
}