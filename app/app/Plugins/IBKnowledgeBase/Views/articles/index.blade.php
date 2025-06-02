@extends('web.layouts.layout')

@section('content')
    <x-breadcrumbs.list>
        <x-breadcrumbs.item href="/" label="Главная">Главная</x-breadcrumbs.item>
        <x-breadcrumbs.item current="true" href="/" label="Главная">База знаний по ИБ</x-breadcrumbs.item>
    </x-breadcrumbs.list>

    <div class="album">
        <div class="container">

            <h2 class="title my-0 mb-3">База знаний по ИБ</h2>

            <div class="d-flex gap-1 mb-3 flex-wrap">
                @foreach (array_unique(explode(';', implode(';', $articles->pluck('tags')->toArray()))) as $tag)
                    <a href="{{ route('web.plugins.ibkb.articles.index', ['tag' => $tag]) }}" type="button"
                        @class([
                            'btn',
                            'btn-secondary' => $tag !== request()->input('tag', null),
                            'btn-primary' => $tag == request()->input('tag', null),
                        ]) class="btn btn-secondary"
                        style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                        {{ $tag }}
                    </a>
                @endforeach
            </div>

            <div class="row">
                @foreach ($articles as $article)
                    <div class="col-md-4">
                        <div class="card mb-4 box-shadow">
                            @if ($article->pictures->count())
                                <img class="card-img-top"
                                    data-src="holder.js/100px225?theme=thumb&amp;bg=55595c&amp;fg=eceeef&amp;text=Thumbnail"
                                    src="{{ $article->pictures->first()->url() }}" data-holder-rendered="true"
                                    style="height: 225px; width: 100%; display: block;">
                            @endif
                            <div class="card-body">
                                <p class="card-text">{{ $article->title }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-secondary">Подробнее</button>
                                    </div>
                                    <small class="text-muted">{{ $article->created_at->format('d.m.Y H:i') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
@endsection
