$(document).ready(function () {
    initSortableMatrix();
    initRelationSelectForms();
});

function updateSortableMatrix(input = ".matrix ._sortable") {
    var counter = 0;

    $.each($(input), function () {
        counter += 100;
        $(this).val(counter);
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