<nav class="navbar navbar-expand-lg bg-light mb-3">
    <div class="container-fluid container">
        <a class="navbar-brand" href="/">{{ config('app.name') }}</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll"
            aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarScroll">
            <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
                @if (auth()->user())
                    <x-layout.nav-link activeis="{{ request()->is('home') }}" href="{{ route('web.home.index') }}"
                        label="Главная" />
                    @if (auth()->user()->hasAnyAccess(['platform.min.base']))
                        @if (empty(config('content.datalens.url')) == false)
                            <x-layout.nav-link href="{{ config('content.datalens.url') }}" label="Дашборд" />
                        @endif
                        <x-layout.nav-link activeis="{{ request()->is('minister.reports') }}"
                            href="{{ route('web.minister.reports') }}" label="Учреждения" />
                        <x-layout.nav-link activeis="{{ request()->is('minister.by-form') }}"
                            href="{{ route('web.minister.by-form') }}" label="Отчеты" />
                    @endif
                    @if (auth()->user()->hasAccess('platform.index'))
                        <x-layout.nav-link href="{{ route('platform.index') }}" label="Конфигуратор" />
                    @endif
                    @if (auth()->user()->hasAccess('form-checker'))
                        <x-layout.nav-link activeis="{{ request()->is('form-checker') }}"
                            href="{{ route('web.form-checker.index') }}" label="Проверка" />
                    @endif
                    @if (config('app.custom_reports'))
                        @if (empty(request()->user()) == false && request()->user()->hasAnyAccess(['platform.custom-reports.loading']))
                            <x-layout.nav-link activeis="{{ request()->is('custom-reports') }}"
                                href="{{ route('web.custom-reports.index') }}" label="Кастомные отчеты" />
                        @endif
                    @endif
                @endif
            </ul>
            @if (empty(request()->user()))
                <a href="{{ route('web.auth.login.index') }}"
                    class="btn btn-outline-primary d-flex align-items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-person" viewBox="0 0 16 16">
                        <path
                            d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z" />
                    </svg>
                    Авторизоваться
                </a>
            @else
                <div class="dropdown text-end">
                    <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle"
                        data-bs-toggle="dropdown" aria-expanded="false">

                        @if ($attachment = auth()->user()->avatar())
                            <img src="{{ $attachment->url() }}" alt="mdo" width="32" height="32"
                                class="rounded-circle" />
                        @else
                            <img src="{{ asset('img/default-avatar.svg') }}" alt="mdo" width="32"
                                height="32" class="rounded-circle" />
                        @endif
                    </a>
                    <ul class="dropdown-menu text-small" aria-labelledby="dropdownUser1" style="">
                        <li>
                            <button class="user-edit dropdown-item">Редактировать</button>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('web.auth.logout.index') }}">Выйти</a>
                        </li>
                    </ul>
                </div>
            @endif
        </div>

    </div>
</nav>
