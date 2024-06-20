$(document).ready(function () {
    initSortableMatrix();
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

    if (0 < $(input).length) {
        init();
    } else {
        $("a[data-action=\"matrix#addRow\"]").click(function () {
            if ($(this).closest(".matrix").find(".ui-sortable").length == 0) {
                setTimeout(() => {
                    updateSortableMatrix(input);
                    init();
                }, 1000);
            }
        });
    }
}