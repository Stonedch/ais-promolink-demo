@extends('web.layouts.layout')

@section('content')
    <x-breadcrumbs.list>
        <x-breadcrumbs.item href="/">Главная</x-breadcrumbs.item>
        <x-breadcrumbs.item current="true" href="/custom-reports">Загрузка документа</x-breadcrumbs.item>
    </x-breadcrumbs.list>

    <x-custom-report.base />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/diff-match-patch/1.0.5/index.min.js"></script>

    @if (@$customReportTypes->count() && $customReportTypes->whereNotNull('attachment_id')->count())
        <div class="container mt-3">
            <h2>Шаблоны документов</h2>
            <table class="table m-0">
                <thead>
                    <tr>
                        <th scope="col">
                            <span>Тип загружаемого документа</span>
                        </th>
                        <th scope="col">
                            <span>Шаблон</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customReportTypes->whereNotNull('attachment_id') as $type)
                        <tr>
                            <td class="align-middle">
                                <b>{{ $type->title }}</b>
                            </td>
                            <td class="align-middle">
                                <a class="btn btn-primary btn-sm opacity-75"
                                    href="{{ route('web.custom-reports.download-template', ['id' => $type->id]) }}">Скачать</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if (@$customReports->count())
        <div class="container mt-3 mb-3">
            <h2>Загруженные документы</h2>
            <table class="table m-0">
                <thead>
                    <tr>
                        <th scope="col">
                            <span>№</span>
                        </th>
                        <th scope="col">
                            <span>Тип загружаемого документа</span>
                        </th>
                        <th scope="col">
                            <span>Загружаемый документ от</span>
                        </th>
                        <th scope="col">
                            <span>Статус</span>
                        </th>
                        <th scope="col">
                            <span>Информация об ошибке</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customReports as $report)
                        <tr data-report-id="{{ $report->id }}">
                            <td class="align-middle">
                                <small>{{ $report->id }}</small>
                            </td>
                            <td class="align-middle">
                                <b>{{ $customReportTypes->get($report->custom_report_type_id)->title }}</b>
                            </td>
                            <td class="align-middle">
                                {{ $report->created_at->format('d.m.Y H:i') }}
                            </td>
                            <td class="align-middle text-nowrap">
                                {{ @$report->getWorkedStatus() }}
                            </td>
                            <td class="align-middle _error-message">
                                {{ nl2br($report->getWorkedErrorMessage('; ', true)) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <script>
        $(document).ready(function() {
            $("._error-message").each(function() {
                const $errorBlock = $(this);
                const inputString = $errorBlock.text().trim();

                const blocks = inputString.split(/\[|\]/)
                    .map(block => block.trim())
                    .filter(block => block !== '');

                const valuesBlock = blocks.find(block =>
                    block.startsWith('значение в документе') &&
                    block.includes('в шаблоне')
                );

                if (!valuesBlock) return;

                const docValue = valuesBlock.match(/документе: "([^"]+)"/)?.[1] || '';
                const templateValue = valuesBlock.match(/шаблоне: "([^"]+)"/)?.[1] || '';

                const normalizeText = (text) => {
                    return text;
                    if (!text) return '';
                    return text
                        .replace(/<br\s*\/?>/gi, ' ')
                        .replace(/\s+/g, ' ')
                        .trim();
                };

                const normalizedDoc = normalizeText(docValue);
                const normalizedTemplate = normalizeText(templateValue);

                const dmp = new diff_match_patch();
                const diffs = dmp.diff_main(normalizedTemplate, normalizedDoc);
                dmp.diff_cleanupSemantic(diffs);

                const diffHtml = dmp.diff_prettyHtml(diffs)
                    .replace(/&para;/g, '<br>');

                $errorBlock.html(`
                    <small>${inputString}</small>
                    <div style="
                        background: #f0f0f0; 
                        padding: 10px; 
                        margin-top: 5px; 
                        border-radius: 4px;
                        overflow-x: auto;
                    ">
                        <b>Различия в значениях:</b><br>${diffHtml}
                    </div>
                `);
            });
        });
    </script>

@endsection
