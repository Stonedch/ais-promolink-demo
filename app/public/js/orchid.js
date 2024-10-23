$(document).ready(function () {
    initSortableMatrix();
    initRelationSelectForms();
    initModalRow();
    initSluggableMatrix();
    renderGroupSelect();
    initModalStructure();
});

function initModalStructure() {
    $("._open-modal-structure").click(function (event) {
        event.preventDefault();

        const form = $(this).data("form-id");

        new Fancybox([
            {
                src: `/forms/preview-structure/${form}`,
                type: "iframe",
            }],
            {
                dragToClose: false,
                width: "90vw",
                height: "90vh",
            }
        )
    });
}

function renderGroupSelect() {
    const render = (withBackValues = false) => {
        const options = {};

        $.each($("input[name$=\"[name]\"][name^=\"groups\"]"), function () {
            const slug = $(this).closest("tr").find("input[name$=\"[slug]\"][name^=\"groups\"]").val();
            const name = $(this).val();
            options[slug] = name;
        });

        $.each($("select._group-select"), function () {
            const select = $(this);
            const value = select.val();

            select.html("<option value=\"\" selected=\"\">-</option>");

            $.each(options, (slug, name) => {
                select.append(`<option value="${slug}">${name}</option>`);
            });

            if (withBackValues) {
                select.val($(select).data("value")).change();
            } else {
                select.val(value).change();
            }
        });

        var template = $("._group-select").closest("table").find(".matrix-template")[0];
        var templateOptions = "<option value=\"\" selected=\"\">-</option>";

        $.each(options, (slug, name) => templateOptions += `<option value=\"${slug}\">${name}</option>`);
        template.content.querySelector("select._group-select").innerHTML = templateOptions;
    }

    if (0 < $("._group-select").length) {
        render(true);
        $("a[data-action=\"matrix#addRow\"]").click(function (event) {
            $("select._group-select").on("click", () => render());
        });
    }
}

function initSluggableMatrix(event) {
    const init = function () {
        $.each(
            $("._sluggable"),
            function () {
                if ($(this).data("sluggable-setted") != "1" && $(this).val() == "") {
                    const date = new Date();
                    $(this).val(date.getTime());
                    $(this).data("sluggable-setted", 1);
                }
            }
        );
    }

    init();

    $("a[data-action=\"matrix#addRow\"]").click(function (event) {
        init();
    });
}

function showModalRow(event) {
    new Fancybox([
        {
            src: `
				<div class="_modal-inputs__container">
                    <div class="_modal-inputs bg-white p-4 py-4 d-flex flex-column">
                    </div>
                    <div class="bg-light px-4 py-3 d-flex justify-content-end">
                        <div class="">
                            <div class="form-group mb-0">
                                <button class="btn  btn-default _modal-inputs__submit">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" class="me-2" viewBox="0 0 16 16" role="img" path="bs.check-circle" componentname="orchid-icon">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"></path>
                                        <path d="m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"></path>
                                    </svg>
                                    <span>Сохранить</span>
                                </button>
                            </div>
                        </div>
                    </div>
				</div>
			`,
            type: "html",
        },
    ], {
        dragToClose: false,
        on: {
            done: () => {
                var row = 0;
                var groups = $(event.target).closest("table").find(".matrix-template")[0].content.querySelectorAll(".form-group");

                $.each(groups, function () {
                    row += 1;

                    $("._modal-inputs").append(this.cloneNode(true));
                    $("._modal-inputs .form-group:last-child [disabled=\"disabled\"]").closest(".form-group").hide();

                    var label = $(event.target).closest("table").find(`thead th:nth-child(${row})`).text();
                    $("._modal-inputs .form-group:last-child").prepend(`<label class="form-label">${label}</label>`);

                    var name = $(event.target).closest("tr").find(`th:nth-child(${row}) input, th:nth-child(${row}) select`).attr("name");
                    $("._modal-inputs .form-group:last-child input, ._modal-inputs .form-group:last-child select").attr("name", name);

                    if ($(event.target).closest("tr").find(`th:nth-child(${row}) select`).length) {
                        var value = $(event.target).closest("tr").find(`th:nth-child(${row}) select option:selected`).val();
                        $("._modal-inputs .form-group:last-child select").val(value).change();
                    } else if ($(event.target).closest("tr").find(`th:nth-child(${row}) input`).length) {
                        var value = $(event.target).closest("tr").find(`th:nth-child(${row}) input`).val();
                        $("._modal-inputs .form-group:last-child input").val(value);
                    }
                });

                $("._modal-inputs__submit").click(function () {
                    $.each(
                        $(this).closest("._modal-inputs__container").find("input, select"),
                        function () {
                            var name = $(this).prop("name");
                            var value = $(this).val();

                            if ($(this).is("select")) {
                                $(event.target).closest("tr").find(`[name="${name}"]`).val(parseInt(value)).change();
                                $(event.target).closest("tr").find(".ts-control .item").data("value", value);
                                $(event.target).closest("tr").find(`[name="${name}"]`).closest("th").find(".ts-control .item").data("value", value);
                                $(event.target).closest("tr").find(`[name="${name}"]`).closest("th").find(".ts-control .item").text(
                                    $(event.target).closest("tr").find(`[name="${name}"] option[value="${value}"]`).text()
                                );
                            } else if ($(this).is("input")) {
                                $(event.target).closest("tr").find(`[name="${name}"]`).val(value);
                            }
                        }
                    );

                    Fancybox.close();
                });

                // Это нужно переписать на универсальность
                const renderParentSelects = () => {
                    const value = $("._modal-inputs select.--select-parent").val();

                    console.log(value);

                    $("._modal-inputs .form-group:has(.--select-field-type)").hide();
                    $("._modal-inputs .form-group:has(.--select-group-type)").hide();

                    if (value == 100) {
                        $("._modal-inputs .form-group:has(.--select-field-type)").show();
                    } else if (value == 200) {
                        $("._modal-inputs .form-group:has(.--select-group-type)").show();
                    }
                };

                $(".--select-parent").on("change", () => renderParentSelects());

                renderParentSelects();
            },
        },
    });

}

function initModalRow() {
    $("._modal-row .form-group").addClass("_disabled");
}

function updateSortableMatrix(input = ".matrix ._sortable") {
    $.each($(".matrix"), function () {
        var counter = 0;

        $.each($(this).find("._sortable"), function () {
            counter += 100;
            $(this).val(counter);
        });
    });
}

function initSortableMatrix(input = ".matrix ._sortable") {
    const init = () => {
        $(input).closest("tbody").sortable({
            out: function () {
                updateSortableMatrix(input);
            },
        });
    }

    init();

    $("a[data-action=\"matrix#addRow\"]").click(function (event) {
        if ($(this).closest(".matrix").find(".ui-sortable").length == 0) {
            init();
        }

        updateSortableMatrix(input);
    });
}

function initRelationSelectForms() {
    const filter = (mainSelect, filteringSelect, paramKey) => {
        $(mainSelect).change(function () {
            const value = $(this).val();

            if (value == "") {
                $(`${filteringSelect} .ts-dropdown-content>*:not([data-value=""])`).show();
                return;
            }

            $(filteringSelect)
                .val(null)
                .change()
                .closest(".form-group")
                .find(".ts-control>.item")
                .text(null);

            $(`${filteringSelect} .ts-dropdown-content>*:not([data-value=""])`).hide();

            params = {};
            params[paramKey] = value;

            $.get("/api/event-store", params, function (response) {
                $.each(response.data.forms, function (form) {
                    $(`${filteringSelect} .ts-dropdown-content>*[data-value="${form}"]`).show();
                });
            });
        });
    };

    filter("._relation-departament-type", "._relation-departament-type-forms", "departamentType");
    filter("._relation-districts", "._relation-district-forms", "district");
}