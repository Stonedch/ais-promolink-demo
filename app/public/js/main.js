import { PLAlert } from "./pl-assets/pl-alerts.js";
import { PLCustomReport } from "./pl-assets/pl-custom-report.js";
import { PLNotification } from "./pl-assets/pl-notification.js";

window.onload = () => {
    (new PLAlert(".alert")).initialCloseButtonEvents(".close-button");
    (new PLCustomReport(".custom-report-form")).initialSubmitButton();
    (new PLNotification(".notification-card")).initialShowEvent();
};
