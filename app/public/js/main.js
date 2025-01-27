import { PLAlert } from "./pl-assets/pl-alerts.js";

document.addEventListener("DOMContentLoaded", function () {
    (new PLAlert(".alert")).initialCloseButtonEvents(".close-button");
});