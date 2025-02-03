@if (config('app.custom_reports'))
    @if (empty($types) == false)
        <div class="container">
            <div class="mb-4 bg-light p-3 rounded">
                <h3>Загрузка кастомного отчета</h3>
                <form id="customReportForm" class="custom-report-form">
                    <div class="row mb-3">
                        <div class="col-5">
                            <label class="form-label">Тип отчета</label>
                            <select class="form-select" name="custom_report_type_id">
                                <option value selected>Выберите тип отчета</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type->id }}">{{ $type->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-5">
                            <label for="customFormFile" class="form-label">Документ</label>
                            <input class="form-control" type="file" id="customFormFile" name="attachment">
                        </div>
                        <div class="col-2 d-flex">
                            <button type="submit"
                                class="submit-button btn btn-primary mt-auto w-100">Отправить</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endif
