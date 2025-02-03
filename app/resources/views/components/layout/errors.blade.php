@if (@$errors->any())
    <script type="module">
        import { PLAlert } from "/js/pl-assets/pl-alerts.js";

        window.onload = () => {
            (new PLAlert())
                .show("Ошибка", `<ul class="m-0">{!! implode('', $errors->all('<li>:message</li>')) !!}</ul>`, PLAlert.TYPE_DANGER)
                .initialCloseButtonEvents();
        };
    </script>
@endif
