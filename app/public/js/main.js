import { PLAlert } from "./pl-assets/pl-alerts.js";
import { PLCustomReport } from "./pl-assets/pl-custom-report.js";

window.onload = () => {
    (new PLAlert(".alert")).initialCloseButtonEvents(".close-button");
    (new PLCustomReport(".custom-report-form")).initialSubmitButton();
};
