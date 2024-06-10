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
    if (0 < $(input).length) {
        $(input).closest("tbody").sortable({
            out: function () {
                updateSortableMatrix(input);
            },
        });
    }
}