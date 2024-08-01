$(document).ready(function() {
    ymaps.ready(function() {
        var map = new ymaps.Map("external-departament-map", {
            controls: [
                "searchControl"
            ],
            center: [56.30, 43.98],
            zoom: 8,
            theme: "custom#dark",
        }, {
            searchControlProvider: "yandex#search",
            yandexMapDisablePoiInteractivity: true,
        });

        var clustererPlacemarks = [];
        const clusterer = new ymaps.Clusterer({
            gridSize: 64,
            preset: "islands#bluePersonCircleIcon",
        });

        $.each($("#external-departament-points>*"), function() {
            const point = [$(this).data("latitude"), $(this).data("longitude")];
            const caption = $(this).data("orgsokrname");
            const id = $(this).data("id");

            var placemark = new ymaps.Placemark(point, {
                iconCaption: caption,
                id: id,
                balloonHeader: "Заголовок балуна",
                balloonContent: `
                    <div class="external-departament-point">
                        <p><b>Наименование организации:</b> ${$(this).data("orgname")}</p>
                        <p><b>Сокращенное наименование организации:</b> ${$(this).data("orgsokrname")}</p>
                        <p><b>Полное наименование организации:</b> ${$(this).data("orgpubname")}</p>
                        <p><b>Тип:</b> ${$(this).data("type")}</p>
                        <p><b>Задача организации:</b> ${$(this).data("orgfunc")}</p>
                        <br>
                        <p><b>Должность руководителя:</b> ${$(this).data("post")}</p>
                        <p><b>ФИО руководителя:</b> ${$(this).data("rukfio")}</p>
                        <br>
                        <p><b>Регион:</b> ${$(this).data("region")}</p>
                        <p><b>Город:</b> ${$(this).data("town")}</p>
                        <p><b>Район:</b> ${$(this).data("area")}</p>
                        <p><b>Улица:</b> ${$(this).data("street")}</p>
                        <p><b>Дом:</b> ${$(this).data("house")}</p>
                        <br>
                        <p><b>E-mail:</b> ${$(this).data("mail")}</p>
                        <p><b>Телефон:</b> ${$(this).data("telephone")}</p>
                        <p><b>Факс:</b> ${$(this).data("fax")}</p>
                        <p><b>Доп. телефон:</b> ${$(this).data("telephonedop")}</p>
                        <p><b>Сайт:</b> ${$(this).data("url")}</p>
                        <br>
                        <p><b>ОКПО:</b> ${$(this).data("okpo")}</p>
                        <p><b>ОГРН:</b> ${$(this).data("ogrn")}</p>
                        <p><b>ИНН:</b> ${$(this).data("inn")}</p>
                        <br>
                        <p><b>Расписание:</b> ${$(this).data("schedule")}</p>
                    </div>
                `,
            }, {
                preset: "islands#bluePersonCircleIcon",
                iconColor: colorLuminance(getRandomColor(id)),
                balloonMaxWidth: 500,
                balloonMaxHeight: 700,
            });

            clustererPlacemarks.push(placemark);
        });

        clusterer.add(clustererPlacemarks);
        map.geoObjects.add(clusterer);
    });

    const getRandomColor = function(salt = null) {
        if (salt != null) salt = 1 / salt;
        if (salt == null) salt = Math.random();
        var color = "#" + Math.floor(salt * 16777215).toString(16);
        return color;
    };

    function colorLuminance(hex, lum) {
        hex = String(hex).replace(/[^0-9a-f]/gi, '');
        if (hex.length < 6) {
            hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
        }
        lum = lum || 0;

        var rgb = "#",
            c, i;
        for (i = 0; i < 3; i++) {
            c = parseInt(hex.substr(i * 2, 2), 16);
            c = Math.round(Math.min(Math.max(0, c + (c * lum)), 255)).toString(16);
            rgb += ("00" + c).substr(c.length);
        }

        return rgb;
    }
});