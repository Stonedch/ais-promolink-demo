@include('web.layouts.imports')

<section class="report --edit-blockeds">
    <div class="report__container container-wrap">
        <div id="report_form"></div>
    </div>
</section>

<script type="text/javascript">
    $(document).ready(function() {
        renderFromDataset(
            "#report_form",
            {!! $structure !!},
            {!! json_encode($groups, JSON_UNESCAPED_UNICODE) !!},
            {!! json_encode($collections, JSON_UNESCAPED_UNICODE) !!},
            {!! json_encode($collectionValues, JSON_UNESCAPED_UNICODE) !!}
        );
    });
</script>