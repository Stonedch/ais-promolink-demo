<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }}</title>
    @include('web.layouts.imports')
</head>

<body>

    <div aria-live="polite" aria-atomic="true" class="position-relative">
        <div class="toast-container position-absolute top-0 end-0 p-3">
            <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <svg class="bd-placeholder-img rounded me-2" width="20" height="20"
                        xmlns="http://www.w3.org/2000/svg" aria-hidden="true" preserveAspectRatio="xMidYMid slice"
                        focusable="false">
                        <rect width="100%" height="100%" fill="#d90f0f"></rect>
                    </svg>
                    <strong class="me-auto">Демонстрационная версия</strong>
                </div>
                <div class="toast-body">
                    Данная сборка является демоверсией программного обеспечения.
                    Функционал может быть ограничен, а стабильность работы не гарантируется.
                </div>
            </div>
        </div>
    </div>

    <x-layout.header />
    <x-layout.errors />

    <main>
        @yield('content')
    </main>
</body>

</html>
