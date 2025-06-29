@extends('web.layouts.layout')

@section('content')
    <div class="container bg-light p-5 rounded mb-4">
        <h1 class="mb-3 w-100">Выполните вход</h1>
        <form action="{{ route('web.auth.login.login') }}" enctype="multipart/form-data" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Логин</label>
                <input type="text" class="form-control" name="phone">
            </div>
            <div class="mb-3">
                <label class="form-label">Пароль</label>
                <input type="password" class="form-control" name="password">
            </div>
            <div class="d-flex gap-3 align-items-center">
                <button class="btn btn-primary">Войти</button>
                @if (\App\Plugins\Esia\Providers\EsiaServiceProvider::isActive())
                    @include('Esia::button-trigger')
                @endif
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" checked="checked" name="remember">
                    <label class="form-check-label">Запомнить меня</label>
                </div>
            </div>
        </form>
    </div>

    <div class="container">
        <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
            <div class="col-md-4 d-flex align-items-center cursor-pointer">
                <a href="https://promolink.ru/" class="mb-3 me-2 mb-md-0 text-muted text-decoration-none lh-1">
                    <span class="mb-3 mb-md-0 text-muted">© {{ now()->format('Y') }} PromoLink</span>
                </a>
            </div>
        </footer>
    </div>
@endsection
