import { PLAlert } from "./pl-assets/pl-alerts.js";
import { PLCustomReport } from "./pl-assets/pl-custom-report.js";
import { PLFormSort } from "./pl-assets/pl-form-sort.js";
import { PLNotification } from "./pl-assets/pl-notification.js";
import { PLUserEdit } from "./pl-assets/pl-user-edit.js";

window.onload = () => {
    (new PLAlert(".alert")).initialCloseButtonEvents(".close-button");
    (new PLCustomReport(".custom-report-form")).initialSubmitButton();
    (new PLNotification(".notification-card")).initialShowEvent();
    (new PLUserEdit(".user-edit")).initialShowEvent();
    (new PLFormSort()).initialSort();
};
