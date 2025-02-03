import { PLModal } from "./pl-modal.js";

export class PLNotification {
    #selector;

    constructor(selector = ".notification-card") {
        this.#selector = selector;
    }

    initialShowEvent() {
        _.forEach([...document.querySelectorAll(this.#selector)], function (notification) {
            notification.addEventListener("click", function () {
                const notificationId = notification.dataset.id;
                const title = notification.dataset.title;
                const message = notification.dataset.message;

                // console.log([
                //     notificationId,
                //     title,
                //     message,
                // ]);

                fetch(`/api/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(() => {
                    _.forEach([...notification.querySelectorAll(".notification-new")], function (status) {
                        status.remove();
                    });

                    PLModal.show(/* HTML */ `
                        <div class="bg-light">
                            <h3>${title}</h3>
                            <p class="mb-0">${message}</p>
                        </div>
                    `);
                })
            });
        });
    }
}