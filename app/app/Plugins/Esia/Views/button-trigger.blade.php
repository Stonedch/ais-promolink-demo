<button class="btn btn-outline-success" data-action="esia-trigger">Войти через ГосУслуги</button>

<script>
    $(document).ready(function () {
        $("[data-action=\"esia-trigger\"]").click(onEsiaTrigger);
    });

    function onEsiaTrigger(event) {
        event.preventDefault();
        $.get("/api/plugins/esia/url", response => window.location.href = response.data.url);
    }
</script>
