@php $version = 2; @endphp

<script type="text/javascript" src="/js/jquery-3.7.1.min.js?version={{ config('webassets.version') }}"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css?version={{ config('webassets.version') }}">
<script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js?version={{ config('webassets.version') }}"></script>

<script src="/plugins/fancyapps/ui/dist/index.umd.js?version={{ config('webassets.version') }}"></script>
<link rel="stylesheet" href="/plugins/fancyapps/ui/dist/fancybox/fancybox.css?version={{ config('webassets.version') }}" />

<link rel="stylesheet" href="/plugins/bootstrap/bootstrap.min.css?version={{ config('webassets.version') }}">
<script src="/plugins/bootstrap/bootstrap.bundle.min.js?version={{ config('webassets.version') }}"></script>
<link rel="stylesheet" href="/plugins/bootstrap-themes/zephyr/bootstrap.min.css?version={{ config('webassets.version') }}">

<script src="/plugins/maskedinput/jquery.maskedinput.js?version={{ config('webassets.version') }}"></script>

<script src="/js/momentjs/moment-with-locales.js?version={{ config('webassets.version') }}"></script>

<script src="/js/lodash.js?version={{ config('webassets.version') }}"></script>

<script src="/plugins/swiper/swiper-bundle.js?version={{ $version }}"></script>
<link rel="stylesheet" href="/plugins/swiper/swiper-bundle.css?version={{ config('webassets.version') }}">

<script src="https://api-maps.yandex.ru/2.1/?apikey={{ config('services.yandex.maps') }}&lang=ru_RU"></script>

<link rel="stylesheet" href="/css/styles.css?version={{ config('webassets.version') }}">
<script src="/js/base.js?version={{ config('webassets.version') }}"></script>
<script type="text/javascript" src="/js/interface.js?version={{ config('webassets.version') }}"></script>
<script type="module" src="/js/main.js?version={{ config('webassets.version') }}"></script>
