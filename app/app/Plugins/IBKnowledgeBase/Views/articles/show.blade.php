@extends('web.layouts.layout')

@section('content')
    <x-breadcrumbs.list>
        <x-breadcrumbs.item href="/" label="Главная">Главная</x-breadcrumbs.item>
        <x-breadcrumbs.item href="{{ route('web.plugins.ibkb.articles.index') }}" label="Главная">
            База знаний по ИБ
        </x-breadcrumbs.item>
        <x-breadcrumbs.item current="true" href="{{ route('web.plugins.ibkb.articles.show', $article->id) }}" label="Главная">
            {{ $article->title }}
        </x-breadcrumbs.item>
    </x-breadcrumbs.list>

    <div class="container">
        <div class="row">
            <div class="col-md-8 blog-main">
                <h3 class="pb-3 mb-4 font-italic border-bottom">{{ $article->title }}</h3>
                <p class="blog-post-meta">{{ $article->created_at->format('d.m.Y H:i') }}</p>
                <div class="blog-post">{!! $article->content !!}</div>
                @if ($article->parent_id)
                    <nav class="blog-pagination">
                        <a class="btn btn-outline-primary"
                            href="{{ route('web.plugins.ibkb.articles.show', $article->parent_id) }}">
                            Вернуться
                        </a>
                    </nav>
                @endif
            </div>
            <aside class="col-md-4 blog-sidebar">
                @if (empty($article->tags) == false)
                    <div class="p-3">
                        <h4 class="font-italic">Теги</h4>
                        <ol class="list-unstyled mb-0">
                            @foreach (explode(';', $article->tags) as $tag)
                                <li>
                                    <a href="{{ route('web.plugins.ibkb.articles.index', ['tag' => $tag]) }}">
                                        #{{ $tag }}
                                    </a>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                @endif
                @if ($article->attachment->count())
                    <div class="p-3">
                        <h4 class="font-italic">Документы</h4>
                        <ol class="list-unstyled">
                            @foreach ($article->attachment as $attachment)
                                <li><a href="{{ $attachment->url }}" download>{{ $attachment->original_name }}</a></li>
                            @endforeach
                        </ol>
                    </div>
                @endif
            </aside>
        </div>
    </div>

    @if ($article->attachment->count())
        <div class="container">
            <div class="row mt-5">
                @foreach ($article->attachment as $attachment)
                    <div class="col-md-6">
                        <div class="card flex-md-row mb-4 box-shadow h-md-250">
                            <div class="card-body d-flex flex-column align-items-start">
                                <strong class="d-inline-block mb-2 text-primary">{{ $attachment->extension }}</strong>
                                <h3 class="mb-0">
                                    <a class="text-dark"
                                        href="{{ $attachment->url }}">{{ $attachment->original_name }}</a>
                                </h3>
                                <div class="mb-1 text-muted">{{ $attachment->created_at->format('d.m.Y H:i') }}</div>
                                @if (empty($attachment->description) == false)
                                    <p class="card-text mb-auto">
                                        {{ $attachment->description }}
                                    </p>
                                @endif
                            </div>
                            <div class="card-image-container">
                                @if (str_contains($attachment->mime, 'image'))
                                    <img class="card-img-right flex-auto d-none d-md-block"
                                        data-src="holder.js/200x250?theme=thumb" alt="Thumbnail [200x250]"
                                        src="{{ $attachment->url }}" data-holder-rendered="true">
                                @else
                                    <img class="card-img-right flex-auto d-none d-md-block"
                                        data-src="holder.js/200x250?theme=thumb" alt="Thumbnail [200x250]"
                                        src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22200%22%20height%3D%22250%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20200%20250%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_1972fe9bce0%20text%20%7B%20fill%3A%23eceeef%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A13pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_1972fe9bce0%22%3E%3Crect%20width%3D%22200%22%20height%3D%22250%22%20fill%3D%22%2355595c%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2256.1953125%22%20y%3D%22131%22%3EThumbnail%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E"
                                        data-holder-rendered="true">
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if ($subArticles->count())
        <div id="article-album" class="album mt-5 bg-light py-5">
            <div class="container">

                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="title my-0 mb-3">Дочерние статьи</h2>
                    <form action="{{ route('web.plugins.ibkb.articles.show', $article->id) }}" method="get"
                        class="d-flex gap-3 align-items-center">
                        <input type="hidden" name="tag" value="{{ request()->input('tag', null) }}">
                        <div class="form-group">
                            <input name="title" type="text" class="form-control" placeholder="Поиск по названию"
                                value="{{ request()->input('title', null) }}">
                        </div>
                        <button type="submit" class="btn btn-primary">Найти</button>
                    </form>
                </div>

                <div class="d-flex gap-1 mb-3 flex-wrap">
                    <a href="{{ route('web.plugins.ibkb.articles.show', [$article->id, 'tag' => null, 'title' => request()->input('title', null)]) }}"
                        type="button" type="button" @class([
                            'btn',
                            'btn-secondary' => request()->has('tag'),
                            'btn-primary' => request()->has('tag') == false,
                        ])
                        style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                        Все
                    </a>
                    @foreach (array_unique(explode(';', implode(';', $subArticles->pluck('tags')->toArray()))) as $tag)
                        @if (empty($tag) == false)
                            <a href="{{ route('web.plugins.ibkb.articles.show', [$article->id, 'tag' => $tag, 'title' => request()->input('title', null)]) }}"
                                type="button" @class([
                                    'btn',
                                    'btn-secondary' => $tag !== request()->input('tag', null),
                                    'btn-primary' => $tag == request()->input('tag', null),
                                ])
                                style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                                {{ $tag }}
                            </a>
                        @endif
                    @endforeach
                </div>

                <div class="row">
                    @foreach ($subArticles as $subArticle)
                        <div class="col-md-4">
                            <div class="card mb-4 box-shadow">
                                @if ($subArticle->pictures->count())
                                    <div class="card-image-container">
                                        <img class="card-img-top"
                                            data-src="holder.js/100px225?theme=thumb&amp;bg=55595c&amp;fg=eceeef&amp;text=Thumbnail"
                                            src="{{ $subArticle->pictures->first()->url() }}" data-holder-rendered="true"
                                            style="height: 225px; width: 100%; display: block;">
                                    </div>
                                @endif
                                <div class="card-body">
                                    <p class="card-text">{{ $subArticle->title }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="btn-group">
                                            <a href="{{ route('web.plugins.ibkb.articles.show', $subArticle->id) }}"
                                                type="button" class="btn btn-sm btn-outline-secondary">Подробнее</a>
                                        </div>
                                        <small
                                            class="text-muted">{{ $subArticle->created_at->format('d.m.Y H:i') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    @endif

    <style>
        .card-image-container {
            width: 200px;
            height: 250px;
        }

        .card-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        .album .card-image-container {
            height: 225px;
            width: 100%;
            display: block;
        }

        .album .card-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }
    </style>
@endsection
