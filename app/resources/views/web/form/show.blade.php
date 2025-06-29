@extends('web.layouts.layout')

@section('content')
    <x-breadcrumbs.list>
        <x-breadcrumbs.item href="/">Главная</x-breadcrumbs.item>
        <x-breadcrumbs.item current="true" href="$request->url">{{ $form->name }}</x-breadcrumbs.item>
    </x-breadcrumbs.list>

    <div class="px-5">
        <section class="report">
            <div class="report__container container-wrap">
                <div id="report_form"></div>
            </div>
        </section>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            renderReportByID(
                "#report_form",
                {{ $event->id }},
                {{ $form->id }},
                false,
                `{!! json_encode($formCheckerResults->pluck('status', 'field_id')->toArray(), JSON_UNESCAPED_UNICODE) !!}`,
                `{!! base64_encode(json_encode($data, JSON_HEX_QUOT + JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES)) !!}`
            );
        });
    </script>

    <style>
        td ::-webkit-scrollbar {
            width: 3px;
            box-shadow: none;
            background: #afbceb;
        }

        td ::-webkit-scrollbar-track {
            box-shadow: none;
        }

        td ::-webkit-scrollbar-thumb {
            box-shadow: none;
            background: #3459e6;
        }

        .table__container tbody tr td.--blocked {
            position: sticky !important;
            left: 0;
            z-index: 99;
        }
    </style>
@endsection
