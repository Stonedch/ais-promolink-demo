/* test */
function showError(text) {
    new Fancybox([{
        src: '<div style="max-width: 500px; padding: 25px;"><h2 style="margin-bottom:0;">Ошибка!</h2><p>' + text + '</p></div>',
        type: "html",
    }]);
}
function showPreloader() {
    $("body").append("<div id='loading'></div>");
}
function hidePreloader() {
    $("#loading").remove();
}
function showAlert(header, text) {
    header = header || '';
    if (header != '') header = '<h2 style="margin-bottom:0;">' + header + '</h2>';
    new Fancybox([{
        src: '<div style="max-width: 500px; padding: 25px;">' + header + '<p>' + text + '</p></div>',
        type: "html",
    }]);
}
function form2obj(link) {
    var form = $(link).serializeArray();
    var temp_obj = new Array();
    var obj_name = new Array();
    $(form).each(function (key, value) {
        if (typeof temp_obj[value.name] != "object") {
            temp_obj[value.name] = new Array();
        }
        temp_obj[value.name].push(value.value)
        obj_name.push(value.name);
    });
    var obj = {};
    $.each(obj_name, function (index, value) {
        if (temp_obj[value].length > 1) {
            obj[value] = temp_obj[value];
        } else {
            obj[value] = temp_obj[value].pop();
        }
    });
    return obj;
}
function makePost(link, obj, callback, file_arr = false) {
    showPreloader();

    var formData = new FormData();
    $.each(obj, function (k, v) {
        if (typeof v == "object") {
            $.each(v, function () {
                formData.append(k, this);
            })
        } else {
            formData.append(k, v);
        }
    })
    if (file_arr !== false) {
        if (typeof file_arr == "string") {
            file_arr = new Array($(file_arr));
        }
        console.warn(file_arr);

        $.each(file_arr, function () {
            console.warn($(this));
            var filename = $(this).attr("name");
            var link = $(this);
            $.each(this.files, function (k, v) {
                formData.append(filename, $(link)[0].files[k]);
            })
        })
    }

    $.ajax(link, {
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,

        success: function (d) {
            hidePreloader();
            if (typeof d == "object") {
                if (d.state === true) {
                    if (typeof callback == "function") {
                        callback(d); return;
                    }
                } else {
                    if (d.error != '') {
                        showError(d.error);
                    }
                    else if (d.data != '') {
                        showError(d.data);
                    }
                    return;
                }
            }
            showError("Возникла ошибка! Пожалуйста, повторите попытку");
        },
    });
}
$(document).ready(function () {
    $(window).scroll(function () {
        if ($(this).scrollTop() > 0) {
            $('body').addClass("scroll");
        }
    });
    try {
        $('input[name=phone]').mask('+9 (999) 999-9999', { autoclear: false });
    } catch { }
    $('input[type=file]').change(function () {
        var fileCount = this.files.length;
        if ($(this).next(".file_trigger").html() != undefined) {
            $(this).next(".file_trigger").html(declOfNum(fileCount, ["Выбран", "Выбрано", "Выбрано"]) + ' ' + fileCount + ' ' + declOfNum(fileCount, ["файл", "файла", "файлов"]));
        }
    });


    // toggle like ios
    $(".toggle").click(function () {
        $(this).toggleClass("on");
    })

})
$.fancyConfirm = function (opts) {
    opts = $.extend(true, {
        title: 'Are you sure?',
        message: '',
        okButton: 'OK',
        noButton: 'Cancel',
        callback: $.noop
    }, opts || {});

    $.fancybox.open({
        type: 'html',
        src:
            '<div class="fc-content">' +
            '<h3>' + opts.title + '</h3>' +
            '<p>' + opts.message + '</p>' +
            '<p class="tright">' +
            '<a data-value="0" data-fancybox-close class="backward">' + opts.noButton + '</a>' +
            '<button data-value="1" data-fancybox-close class="btn button big red">' + opts.okButton + '</button>' +
            '</p>' +
            '</div>',
        opts: {
            animationDuration: 350,
            animationEffect: 'material',
            modal: true,
            baseTpl:
                '<div class="fancybox-container fc-container" role="dialog" tabindex="-1">' +
                '<div class="fancybox-bg"></div>' +
                '<div class="fancybox-inner">' +
                '<div class="fancybox-stage"></div>' +
                '</div>' +
                '</div>',
            afterClose: function (instance, current, e) {
                var button = e ? e.target || e.currentTarget : null;
                var value = button ? $(button).data('value') : 0;

                opts.callback(value);
            }
        }
    });
}
function declOfNum(n, text_forms) {
    n = Math.abs(n) % 100; var n1 = n % 10;
    if (n > 10 && n < 20) { return text_forms[2]; }
    if (n1 > 1 && n1 < 5) { return text_forms[1]; }
    if (n1 == 1) { return text_forms[0]; }
    return text_forms[2];
}
function plTabsSwitcher() {
    $(".tabs-container .tabs-head > div").click(function () {
        var tabs_container = $(this).parent(".tabs-head").parent(".tabs-container");
        $(tabs_container).children(".tabs-head").children('div').removeClass('active');
        $(tabs_container).children(".tabs-content").children('div').removeClass('active');
        var index = $(this).index();
        $(this).addClass('active');
        $(tabs_container).children(".tabs-content").children("div").eq(index).addClass("active");
    });
    $(".tabs-container").each(function () {
        var start_from = $(this).attr("attr-start-from");
        if (typeof start_from == 'undefined') {
            var start_from = 0;
        } else start_from = parseInt(start_from) - 1;
        $(this).children(".tabs-head").children("div").eq(start_from).click();
    });
}
function scroll2elem(link, time = 500) {
    $("html, body").stop().animate({ scrollTop: $(link).offset().top }, time);
}
function sendBXForm(id, callback) {
    var callback = callback || false;
    if (!$("#" + id)[0].reportValidity()) return false;
    var obj = form2obj("#" + id);
    obj['id'] = id;
    makePost("/local/ajax/post.php", obj, function (d) {
        hidePreloader();
        if (d.state === true) {
            if (typeof callback == "function") {
                callback();
            } else {
                showAlert("Отправлено", "Ваше обращение успешно отправлено. Наш специалист свяжется с Вами в ближайшее время!");
            }
            $("#" + id)[0].reset();
        }
    })
}
function fast_search(input, search_elem_root, search_elements) {
    $(input).on("keyup", function (k, v) {
        var q = $(this).val().toString().toUpperCase();
        if (q != '') {
            $(search_elem_root).addClass("fast_search_active")
        } else {
            $(search_elem_root).removeClass("fast_search_active")
        }
        $(search_elem_root).find(search_elements).removeClass('hidden')
        $.each($(search_elem_root).find(search_elements), function (k, v) {
            if ($(this).text().toString().toUpperCase().indexOf(q) < 0) {
                $(this).addClass("hidden");
            }
        })
    })
}