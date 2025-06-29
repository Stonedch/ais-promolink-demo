async function renderReportByID(path, id, formId = null, disabled = false, checkers = null, response = null) {
    showPreloader();

    const render = async (d) => {
        hidePreloader();

        if (!_.has(window, "_app")) {
            window._app = {};
        }

        window._app.form = d.data;
        window._app.current_report_id = id;
        window._app.current_form_id = formId;

        d = d.data;

        var html = "";
        var event = null;

        if (formId == null) {
            if (!_.has(d.events, id)) {
                showError("Отчет не найден!");
                return;
            }

            event = d.events[id];
        } else {
            try {
                event = d.allEvents[formId][id];
            } catch {
                showError("Отчет не найден!");
                return;
            }
        }

        var buttons = "";

        if (disabled == false) {
            buttons = `
                <button class="btn btn-secondary button button--light load-all-old-values -unfolding">Загрузить все значения</button>
                <button class="btn btn-secondary button button--light load-old-values disabled">Загрузить старые значения</button>
                <button class="btn btn-primary button button--light top-save-draft">Сохранить черновик</button>
                <button class="btn btn-success button button--dark top__save" onclick="saveRenderedForm();">Отправить отчет</button>
			`;
        }

        html += `
			<div class="top mb-3">
				<div class="top__container container-wrap d-flex justify-content-between">
                    <h2>Отчет «${d.forms[event.form_id].name}»</h2>
                    <div class="top__buttons btn-group">${buttons}</div>
                </div>
			</div>
			<form id="renderedForm">
    	`;

        var struct =
            typeof event.form_structure == "object"
                ? event.form_structure
                : JSON.parse(event.form_structure);
        var groups = null;

        try {
            groups = d.formGroups[event.form_id];
        } catch { }

        if (struct.form.type == 100) {
            html += renderLineForm(struct, groups);
        } else if (struct.form.type == 200) {
            html += renderTabForm(struct, groups);
        } else if (struct.form.type == 300) {
            html += renderTabForm(struct, groups);
        }

        $(path).html(html + "</form>");

        if (checkers) {
            checkers = $.parseJSON(checkers);

            $.each(checkers, function (id, statusCode) {
                var icon = null;

                switch (statusCode) {
                    case 200:
                        icon = "/img/status-icons/accepted.svg";
                        break;
                    case 300:
                        icon = "/img/status-icons/rejected.svg";
                        break;
                }

                $(`label[data-id="${id}"] .report__name`).html(
                    $(`label[data-id="${id}"] .report__name`).html() + ` <img class="report__status-icon" src="${icon}" />`
                );
            });
        }

        try {
            _.each(struct.fields, function (field) {
                var autocompleteSource = [];
                var lastFieldValue;

                _.each(
                    window._app.form.writedEvents[struct.form.id],
                    function (event) {
                        try {
                            const eventStructure = $.parseJSON(
                                event.form_structure
                            );
                            const eventStructureFieldIdentifier = _.find(
                                eventStructure.fields,
                                (eventStructureField) =>
                                    eventStructureField.name == field.name &&
                                    eventStructureField.type == field.type &&
                                    eventStructureField.group == field.group
                            ).id;

                            const value = _.find(
                                window._app.form.formResults[event.form_id][
                                event.id
                                ],
                                (resultField) =>
                                    resultField.field_id ==
                                    eventStructureFieldIdentifier
                            ).value;

                            if (value) {
                                lastFieldValue = value;
                                autocompleteSource.push(value);
                            }
                        } catch { }
                    }
                );

                $(`#field-${field.id}:not(select)`)
                    .autocomplete({
                        source: autocompleteSource,
                        minLength: 1,
                    })
                    .focus(function () {
                        $(this).autocomplete(
                            "search",
                            $(this).value ? $(this).value : lastFieldValue
                        );
                    });
            });
        } catch {
            console.error("Ошибка обработки структуры");
        }

        $('#renderedForm input[type="number"]').on("keydown", function (event) {
            switch (event.key) {
                case "ArrowUp":
                    event.preventDefault();
                    break;
                case "ArrowDown":
                    event.preventDefault();
                    break;
            }
        });

        if (disabled) {
            $(
                "#renderedForm input, #renderedForm select, #renderedForm textarea"
            ).prop("disabled", true);
        }

        await renderSavedValues();
        updateFormPercent();

        if (disabled == false) {
            $(".top-save-draft").click((event) => saveDraftForm(event, true));
            $(".load-old-values").click((event) => loadOldValues(event));
            $(".load-all-old-values").click((event) => loadAllOldValues(event));
        }

        initMultipleGroupButton();

        $.each($("td textarea"), function () {
            this.style.height = "";
            this.style.height = this.scrollHeight + "px";
        });

        if (struct.form.type == 200 || struct.form.type == 300) {
            addStickyTableHeader($("#renderedForm table"));
        }

    }

    if (response) {
        var Base64 = { _keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=", encode: function (e) { var t = ""; var n, r, i, s, o, u, a; var f = 0; e = Base64._utf8_encode(e); while (f < e.length) { n = e.charCodeAt(f++); r = e.charCodeAt(f++); i = e.charCodeAt(f++); s = n >> 2; o = (n & 3) << 4 | r >> 4; u = (r & 15) << 2 | i >> 6; a = i & 63; if (isNaN(r)) { u = a = 64 } else if (isNaN(i)) { a = 64 } t = t + this._keyStr.charAt(s) + this._keyStr.charAt(o) + this._keyStr.charAt(u) + this._keyStr.charAt(a) } return t }, decode: function (e) { var t = ""; var n, r, i; var s, o, u, a; var f = 0; e = e.replace(/[^A-Za-z0-9\+\/\=]/g, ""); while (f < e.length) { s = this._keyStr.indexOf(e.charAt(f++)); o = this._keyStr.indexOf(e.charAt(f++)); u = this._keyStr.indexOf(e.charAt(f++)); a = this._keyStr.indexOf(e.charAt(f++)); n = s << 2 | o >> 4; r = (o & 15) << 4 | u >> 2; i = (u & 3) << 6 | a; t = t + String.fromCharCode(n); if (u != 64) { t = t + String.fromCharCode(r) } if (a != 64) { t = t + String.fromCharCode(i) } } t = Base64._utf8_decode(t); return t }, _utf8_encode: function (e) { e = e.replace(/\r\n/g, "\n"); var t = ""; for (var n = 0; n < e.length; n++) { var r = e.charCodeAt(n); if (r < 128) { t += String.fromCharCode(r) } else if (r > 127 && r < 2048) { t += String.fromCharCode(r >> 6 | 192); t += String.fromCharCode(r & 63 | 128) } else { t += String.fromCharCode(r >> 12 | 224); t += String.fromCharCode(r >> 6 & 63 | 128); t += String.fromCharCode(r & 63 | 128) } } return t }, _utf8_decode: function (e) { var t = ""; var n = 0; var r = c1 = c2 = 0; while (n < e.length) { r = e.charCodeAt(n); if (r < 128) { t += String.fromCharCode(r); n++ } else if (r > 191 && r < 224) { c2 = e.charCodeAt(n + 1); t += String.fromCharCode((r & 31) << 6 | c2 & 63); n += 2 } else { c2 = e.charCodeAt(n + 1); c3 = e.charCodeAt(n + 2); t += String.fromCharCode((r & 15) << 12 | (c2 & 63) << 6 | c3 & 63); n += 3 } } return t } }
        response = Base64.decode(response);
        render({ data: $.parseJSON(response) });
    } else {
        $.get("/api/forms").done(async (d) => render(d));
    }
}

function renderTabForm(struct, groups = null) {
    var html = `
        <div class="table">
          <table class="table table__container table-bordered mb-0">
            <thead>
	`;

    struct.fields = _.orderBy(struct.fields, ["sort"], ["asc"]);

    var headers = {};

    if (groups) {
        var maxLevel = 0;
        var maxWidth = 0;
        var currentParentId = null;
        var identifiers = [];

        var fieldCounter = 0;

        $.each(groups, function () {
            this['isGroup'] = true;
        });

        $.each(struct.fields, function () {
            try {
                groups.push({
                    name: this.name,
                    id: `f-${this.id}`,
                    parent_id: this.group_id,
                    sort: this.sort,
                    isGroup: false,
                });
            } catch { }
        });

        var unpreparedGroups = groups;
        groups = [];

        $.each(unpreparedGroups, function () {
            if (this.slug == undefined) {
                groups.push(this);
            } else if (0 < _.filter(unpreparedGroups, { parent_id: this.id }).length) {
                groups.push(this);
            }
        });

        while (true) {
            $.each(_.sortBy(_.filter(groups, { parent_id: currentParentId }), ["sort"]), function () {
                identifiers.push(this.id);

                const level = headers[this.parent_id] ? headers[this.parent_id].level + 1 : 1;

                if (maxLevel < level) maxLevel = level;

                if (headers[this.parent_id]) {
                    startPosition = headers[this.parent_id];
                }

                headers[this.id] = {
                    id: this.id,
                    name: this.name,
                    width: 1,
                    level: level,
                    parent: this.parent_id,
                    sort: this.sort,
                    isGroup: this.isGroup,
                    hasChilds: false,
                };
            });

            if (identifiers.length == 0) break;

            currentParentId = identifiers.pop();
        }

        for (i = maxLevel - 1; 0 < i; i--) {
            $.each(_.sortBy(_.filter(headers, { level: i }), ["sort"]), function () {
                const sum = _.sumBy(_.filter(headers, { parent: this.id }), "width");
                if (maxWidth < sum) maxWidth = sum;
                headers[this.id].width = sum ? sum : 1;
            });
        }

        for (var i = 1; i <= maxLevel; i++) {
            var startRow = 1;
            var added = 0;

            $.each(_.sortBy(_.filter(headers, { level: i }), ["sort"]), function () {
                var startPosition = startRow;

                if (this.parent) {
                    const parent = headers[this.parent]
                    headers[this.parent].hasChilds = true;
                    startPosition = parent.startPosition;
                }

                headers[this.id].startPosition = startPosition + added;
                added += 1;
            });
        }

        for (var row = 1; row <= maxLevel; row++) {
            var currentColumn = 1;

            html += `<tr class="p-0">`;

            $.each(_.sortBy(_.filter(headers, { level: row }), ["sort"]), function () {
                var colspan = this.width;
                var rowspan = 1;

                if (this.hasChilds == false) {
                    rowspan = maxLevel - row + 1;

                    if (rowspan <= 0) {
                        rowspan = 1;
                    }
                }

                var fontSize = 12 - (row - 1);

                html += `
                    <td class="p-1 bg-light" rowspan="${rowspan}" colspan="${colspan}">
                        <div class="table__name form-label fw-bold" style="font-size: ${fontSize}px">${this.name}</div>
                    </td>
                `;

                currentColumn = this.startPosition + 1;
            });

            html += "</tr>";
        }
    } else {
        html += "<tr>";
        _.each(struct.fields, function (v, k) {
            html += `<td class=""><div class="table__name">${v.name}</div></td>`;
        });
        html += "</tr>";
    }

    html += `</thead><tbody class="table__row">`;

    var tab_row = "";
    var currentColumn = 1;

    _.each(_.sortBy(struct.fields, ["sort"]), function (v, k) {
        if (v.type == 100) {
            tab_row += /* HTML */`
				<td class="p-0 position-relative" style="max-height: 200px; width: 150px; min-width: 150px; max-width: 150px">
                    <label style="height: 100%; margin-top: 2px; margin-bottom: 2px; display: block;">
                        <textarea
                            placeholder="Введите текст"
                            name="fields[${v.id}][]"
                            class="table__field report__input form-control border-0 rounded-0 w-100 text-start p-1"
                            type="text"
                            style="box-shadow: none; max-height: 200px; min-height: fit-content; resize: none"
                            oninput='this.style.height = "";this.style.height = this.scrollHeight + "px"'></textarea>
                    </label>
                </td>
            `;
        }
        if (v.type == 200) {
            tab_row += /* HTML */ `
				<td class="p-0 position-relative" style="height: 60px; max-height: 200px; width: 150px; min-width: 150px; max-width: 150px">
                    <label style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; width: 100%; height: calc(100% - 4px); margin-top: 2px;">
                        <select class="report__input form-select border-0 rounded-0 w-100 p-1" name="fields[${v.id}][]" style="box-shadow: none;">
                            ${buildSelectOptions(v.collection_id)}
                        </select>
                    </label>
				</td>
            `;
        }
        if (v.type == 300) {
            tab_row += /* HTML */`
				<td class="p-0 position-relative" style="height: 60px; max-height: 200px; width: 150px; min-width: 150px; max-width: 150px">
                    <label style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; width: 100%; height: 100%;">
                        <select class="form-select border-0 rounded-0 w-100 p-1" multiple name="fields[${v.id}][]" style="box-shadow: none; height: calc(100% - 4px); margin-top: 2px; overflow: auto">
                            ${buildSelectOptions(v.collection_id)}
                        </select>
                    </label>
				</td>
            `;
        }
        if (v.type == 400) {
            tab_row += /* HTML */`
				<td class="p-0 position-relative" style="height: 60px; max-height: 200px; width: 150px; min-width: 150px; max-width: 150px">
                    <label style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; width: 100%; height: 100%;">
                        <input
                            type="date"
                            name="fields[${v.id}][]"
                            class="report__input form-control border-0 rounded-0 w-100 p-1"
                            placeholder="Введите текст"
                            style="box-shadow: none;" />
                    </label>
				</td>
            `;
        }
        if (v.type == 500) {
            tab_row += /* HTML */`
				<td class="p-0 position-relative" style="height: 60px; max-height: 200px; width: 150px; min-width: 150px; max-width: 150px">
                    <label style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; width: 100%; height: 100%;">
                        <input
                            type="number"
                            name="fields[${v.id}][]"
                            class="report__input form-control border-0 rounded-0 w-100 p-1"
                            placeholder="Введите значение"
                            style="box-shadow: none;" />
                    </label>
				</td>
            `;
        }
        if (v.type == 700) {
            tab_row += /* HTML */ `
                <td class="p-0 position-relative" style="height: 60px; max-height: 200px; width: 150px; min-width: 150px; max-width: 150px">
                    <label style="width: 100%">
                        <input
                            type="file"
                            multiple="multiple"
                            name="fields[${v.id}][]"
                            class="report__input form-control border-0 rounded-0 w-100 p-1"
                            placeholder="Введите значение"
                            style="box-shadow: none;" />
                    </label>
				</td>
            `;
        }
    });

    window._app.clear__tab_row = tab_row;
    tab_row = "<tr id='row_1'>" + tab_row + "</tr>";

    window._app.renedered_tab_row = tab_row;

    html += tab_row;
    html += "</tbody>";
    html += "</table>";
    html += "</div>";
    html += "</form>";

    if (struct.form.type == 200 || window._app.edit_blockeds) {
        html += /* HTML */`
            <div class="center">
                    <button class="btn btn-success button button--dark " onclick="append_row();">Добавить строку</button>
            </div>
        `;
    }

    if (window._app.edit_blockeds) {
        html += `<div class="center mt-3">
            <button class=" button button--green" onclick="saveBlockedFields()">Сохранить сводную</button>
        </div>`;
    }

    html += `
	<script>
	function append_row(count = 1) {
        var appended_value = "";

        for (let i = 0; i < count; i++) {
          appended_value = appended_value +"<tr id='row_"+(i+2)+"'>"+ window._app.clear__tab_row+"</tr>";
        }

		$("#renderedForm table tbody").append(appended_value);
	}
	</script>
	`;

    return html;
}

function buildSelectOptions(collection_id) {
    try {
        var html = "";
        html += `<option value="" selected>-</option>`;
        _.forEach(
            _.sortBy(window._app.form.collectionValues[collection_id], ['sort']),
            function (v, k) {
                if (v.collection_id != collection_id) return true;
                html += `<option value="${v.id}">${v.value}</option>`;
            }
        );
        return html;
    } catch {
    }
}

async function renderSavedValues(formTarget = "#renderedForm", savedStructureObject = null, withBlockeds = true, results = []) {
    try {
        const eventIdentifier = window._app.current_report_id;
        const formIdentifier = window._app.current_form_id
            ? window._app.current_form_id
            : window._app.form.events[eventIdentifier].form_id;

        var values = [];
        var type = null;

        try {
            type = window._app.form.forms[formIdentifier].type;
            values = results.length ? results : window._app.form.formResults[formIdentifier][eventIdentifier];
        } catch { }

        if (withBlockeds) {
            try {
                await $.get(`/api/forms/form-field-blockeds?form=${formIdentifier}`, function (response) {
                    if (response.state) {
                        var fields = _.groupBy(response.data.blockeds, 'field_id');
                        var maxBlockedIndex = 0;
                        var preparedFields = {};

                        $.each(fields, (fieldIdentifier, fields) => {
                            preparedFields[fieldIdentifier] = _.keyBy(fields, 'index');
                            const maxSlicedBlockedIndex = _.maxBy(fields, "index").index;
                            maxBlockedIndex = maxBlockedIndex < maxSlicedBlockedIndex ? maxSlicedBlockedIndex : maxBlockedIndex;
                        });

                        $.each(preparedFields, (fieldIdentifier, fields) => {
                            $.each(fields, (index, field) => {
                                values.push({
                                    event_id: null,
                                    field_id: fieldIdentifier,
                                    form_id: null,
                                    form_results: null,
                                    id: null,
                                    index: parseInt(index),
                                    user_id: null,
                                    value: field.value,
                                    blocked: true
                                });
                            });
                        });

                    } else {
                        console.error(`Ошибка получения заблокированных строк: ${response.error}`);
                    }
                })
            } catch { }
        }

        var field_type_by_id = {};

        var maxIndex = 0;

        maxIndex = _.maxBy(values, "index").index;

        append_row(maxIndex);

        if (type == 100) {
            try {
                var errorName = null;
                var errorValue = null;
                var errorContainer = null;

                const newRender = (item, container) => {
                    $.each(item["fields"], (name, value) => {
                        errorName = name;
                        errorValue = value;
                        errorContainer = container;

                        $(container.find(`>label>[name="${name}"], >.accordion-collapse>.accordion-body>label>[name="${name}"]`)).val(value);
                    });

                    $.each(item["childs"], (group, forms) => {
                        var subcontainers = $(container).find(`[data-group="${group}"]`);

                        $.each(forms, (index, child) => {
                            if (subcontainers[index] == undefined) {
                                addMultipleGroup(
                                    $(container).find(`[data-group="${group}"]:last-child`).find(">._multiple-group").length
                                        ? $(container).find(`[data-group="${group}"]:last-child`).find(">._multiple-group")
                                        : $(subcontainers[0]).find(">._multiple-group")
                                );

                                subcontainers[index] = $(container).find(`[data-group="${group}"]:nth-child(${index + 1})`);
                            }

                            newRender(child, $(subcontainers[index]).find(">.accordion-collapse>.accordion-body"));
                        });
                    });

                };

                var savedStructure = savedStructureObject
                    ? savedStructureObject
                    : window._app.form.allEvents[formIdentifier][eventIdentifier].saved_structure;


                savedStructure = typeof savedStructure == "object" ? savedStructure : JSON.parse(savedStructure);

                newRender(savedStructure["root"], $("#renderedForm .report__content"));

                $(formTarget).css("display", "block");
                return;
            } catch (e) {
                console.error("Ошибка рендера:", e, errorName, errorValue, errorContainer);
            }
        }

        $.each(values, function (index) {
            if (type == 100) {
                const singleFieldName = `name="fields[${this.field_id}]"`;
                const singleField = `#renderedForm *[${singleFieldName}]`;

                const multipleFieldName = `name="fields[${this.field_id}][]"`;
                const multipleField = `#renderedForm *[${multipleFieldName}]`;

                const index = parseInt(this.index ? this.index : 0);

                $(singleField).val(this.value);
                $($(multipleField)[index]).val(this.value);
            } else if (type == 200 || type == 300) {
                const fieldName = `name="fields[${this.field_id}][]"`;
                const tableRow = `#renderedForm #row_${parseInt(this.index) + 1}`;

                if (field_type_by_id[this.field_id]) {
                    switch (field_type_by_id[this.field_id]) {
                        case 100:
                            $(`${tableRow} textarea[${fieldName}]`).val(this.value);
                            break;
                        case 200:
                            $(`${tableRow} select[${fieldName}]`).val(this.value).change();
                            break;
                        case 300:
                            $(`${tableRow} select[${fieldName}]`).val(this.value).change();
                            break;
                        case 400:
                            $(`${tableRow} input[${fieldName}]`).val(this.value).change();
                            break;
                        case 500:
                            $(`${tableRow} input[${fieldName}]`).val(this.value).change();
                            break;
                    }
                } else {
                    $(`${tableRow} [${fieldName}]:not([type="file"])`).val(this.value);

                    if (0 < $(`${tableRow} [${fieldName}][type="file"]`).length) {
                        var imagesHtml = ``;
                        $.each(this.attachment, function () {
                            imagesHtml += `
                                <a href="${this.url}" data-fancybox target="_blank">
                                    <img src="${this.url}" width="64px"/>
                                    <div data-action="_remove-image" data-id="${this.id}"></div>
                                </a>
                            `;
                        });

                        $(`${tableRow} [${fieldName}][type="file"]`).closest("td").append(`
                            <div class="cell-images">${imagesHtml}</div>
                        `);

                        $(`${tableRow} [${fieldName}][type="file"]`)
                        console.log(this);
                    }

                }

                if (this.blocked) {
                    $(`${tableRow} *[${fieldName}]`).closest("td").addClass("--blocked");
                    $(`${tableRow} *[${fieldName}]`).closest("td").addClass("bg-light");
                    $(`${tableRow} *[${fieldName}]`).closest("td [name]").addClass("bg-light");
                }
            }
        });

        $(`[data-action="_remove-image"]`).click(function (event) {
            event.preventDefault();
            const id = $(this).data("id");
            showPreloader();
            $.get(`/api/forms/remove-attachment?attachment=${id}`).done(function (response) {
                hidePreloader();
                if (response.state) {
                    $(`a:has(>[data-action="_remove-image"][data-id="${id}"])`).remove();
                } else {
                    showError(response.error);
                }
            });
        });


        $(formTarget).css('display', "block");
    } catch (e) {
        console.error(`Ошибка чтения сохранненых записей: ${e}`)
    }
}

function updateFormPercent(
    percentTarget = ".top__percent span",
    formTarget = "#renderedForm",
    url = "/api/forms/percent"
) {
    try {
        const eventIdentifier = window._app.current_report_id;
        const formIdentifier = window._app.current_form_id
            ? window._app.current_form_id
            : window._app.form.events[eventIdentifier].form_id;

        const type = window._app.form.forms[formIdentifier].type;
        const request = { event_id: eventIdentifier };

        if (type == 100) {
            $.post(url, request).done((response) =>
                $(percentTarget).text(response.data.percent <= 100 ? response.data.percent : 100)
            );
        } else if (type == 200) {
            $(percentTarget).parent().hide();
        }
    } catch { }
}

function initMultipleGroupButton() {
    $("._multiple-group").click(function (event) {
        addMultipleGroup($(this));
    });
}

function addStickyTableHeader(link) {
    $("#table_header_sticky_container").remove();
    $(link).before("<div id='table_header_sticky_container'><table class='stylized table-bordered'></table></div>");
    $(link).find("thead").clone().appendTo("#table_header_sticky_container table");
    $("#table_header_sticky_container").width($(link).find("thead").width())
    $("#table_header_sticky_container").css({
        position: "sticky",
        top: "0",
        "z-index": "999",
    });

    $.each($(link).find("thead tr"), function (str_key, str_v) {
        $.each($(link).find("thead tr").eq(str_key).find("td"), function (k, v) {
            $("#table_header_sticky_container thead tr").eq(str_key).find("td").eq(k).css("padding", $(this).css("padding"));
            $("#table_header_sticky_container thead tr").eq(str_key).find("td").eq(k).css("border", $(this).css("border"));
            $("#table_header_sticky_container thead tr").eq(str_key).find("td").eq(k).css("width", $(this).outerWidth() + "px");
        })
    });

    $(link).find("thead").css("visibility", "hidden");
    $(link).find("thead").css("display", "none");

    if (window._app.form.forms[Object.keys(window._app.form.forms)[0]].type == 300) {
        $(link).addClass("pivot");
    }

    $(".navbar.navbar-expand-lg.bg-light.mb-3").addClass("rounded-right");
}

async function saveDraftForm(
    event = null,
    withAlert = false,
    formTarget = "#renderedForm",
    url = "/api/forms/save-draft"
) {
    showPreloader();

    const getStructur = (container) => {
        try {
            var fields = {};
            var childs = {};

            $.each(container.find(">label>*[name],>.accordion-collapse>.accordion-body>label>*[name]"), function () {
                const name = $(this).attr("name");
                const value = $(this).val();

                fields[name] = value;

            });

            $.each(container.find(">.accordion>.accordion-item,>.accordion-collapse>.accordion-body>.accordion>.accordion-item"), function () {
                const subgroup = $(this).data("group");
                if (childs[$(this).data("group")] == undefined) childs[subgroup] = [];
                childs[subgroup].push(getStructur($(this)));
            });

            return { fields: fields, childs: childs };
        } catch {
            return { fields: {}, childs: {} };
        }
    }

    var structur = { "root": getStructur($("#renderedForm>.report__content")) };

    var form = form2obj($("#renderedForm"));
    form.event_id = window._app.current_report_id;

    form["structure"] = JSON.stringify(structur);
    form["json"] = true;

    var formData = new FormData();

    $.each(form, (key, value) => formData.append(key, JSON.stringify(value)));

    $.each($("[type=\"file\"]"), function () {
        const input = this;

        window._lastInput = input;
        var i = 0;

        $.each($(input)[0].files, function () {
            var name = $(input).attr("name").slice(0, -1);
            name += $(input).closest("tr").index() + "]";
            name += `[${i++}]`;
            console.log(name, this);
            formData.append(name, this);
        });
    });

    await $.ajax({
        type: "POST",
        url: url,
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            hidePreloader();

            if (withAlert) {
                if (response.state) {
                    showAlert("Выполнено", "Черновик успешно сохранен!", () => {
                        showPreloader();
                        location.reload();
                    });
                } else {
                    showError(response.error);
                }
            }

        }
    });

    updateFormPercent();
}

async function saveRenderedForm() {
    var obj = form2obj("#renderedForm");
    obj.event_id = window._app.current_report_id;
    showPreloader();

    const getStructur = (container) => {
        try {
            var fields = {};
            var childs = {};

            $.each(container.find(">label>*[name],>.accordion-collapse>.accordion-body>label>*[name]"), function () {
                const name = $(this).attr("name");
                const value = $(this).val();

                fields[name] = value;

            });

            $.each(container.find(">.accordion>.accordion-item,>.accordion-collapse>.accordion-body>.accordion>.accordion-item"), function () {
                const subgroup = $(this).data("group");
                if (childs[$(this).data("group")] == undefined) childs[subgroup] = [];
                childs[subgroup].push(getStructur($(this)));
            });

            return { fields: fields, childs: childs };
        } catch {
            return { fields: {}, childs: {} };
        }
    }

    var structur = { "root": getStructur($("#renderedForm>.report__content")) };

    var form = form2obj($("#renderedForm"));
    form.event_id = window._app.current_report_id;
    form["structure"] = JSON.stringify(structur);
    form["json"] = true;

    var formData = new FormData();

    $.each(form, (key, value) => formData.append(key, JSON.stringify(value)));

    $.each($("[type=\"file\"]"), function () {
        const input = this;

        window._lastInput = input;
        var i = 0;

        $.each($(input)[0].files, function () {
            var name = $(input).attr("name").slice(0, -1);
            name += $(input).closest("tr").index() + "]";
            name += `[${i++}]`;
            console.log(name, this);
            formData.append(name, this);
        });
    });

    await $.ajax({
        type: "POST",
        url: "/api/forms/create",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            hidePreloader();

            if (response.state) {
                showAlert("Выполнено", "Черновик успешно сохранен!", () => {
                    alert("Форма была сохранена!");
                    document.location.href = "/";
                });
            } else {
                showError(response.error);
            }

        }
    });
}
