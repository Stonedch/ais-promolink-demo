@use ('\App\Plugins\IBKnowledgeBase\Models\Article')

<div class="album py-5 bg-light my-5">
    <div class="container">

        <div class="d-flex justify-content-between align-items-center">
            <h2 class="title my-0 mb-3">База знаний по ИБ</h2>
            <form action="{{ route('web.plugins.ibkb.articles.index') }}" method="get"
                class="d-flex gap-3 align-items-center">
                <div class="form-group">
                    <input name="title" type="text" class="form-control" placeholder="Поиск по названию">
                </div>
                <button type="submit" class="btn btn-primary">Найти</button>
                <a href="{{ route('web.plugins.ibkb.articles.index') }}" class="btn btn-outline-primary"
                    role="button">Все статьи</a>
            </form>
        </div>

        <div class="d-flex gap-1 mb-3 flex-wrap">
            @foreach (array_unique(explode(';', implode(';', Article::pluck('tags')->toArray()))) as $tag)
                <a href="{{ route('web.plugins.ibkb.articles.index', ['tag' => $tag]) }}" type="button"
                    class="btn btn-secondary"
                    style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                    {{ $tag }}
                </a>
            @endforeach
        </div>

        <div class="row">
            @foreach (Article::orderBy('id', 'desc')->whereNull('parent_id')->with(['pictures'])->take(9)->get() as $article)
                <div class="col-md-4">
                    <div class="card mb-4 box-shadow">
                        @if ($article->pictures->count())
                            <div class="card-image-container">
                                <img class="card-img-top"
                                    data-src="holder.js/100px225?theme=thumb&amp;bg=55595c&amp;fg=eceeef&amp;text=Thumbnail"
                                    src="{{ $article->pictures->first()->url() }}" data-holder-rendered="true">
                            </div>
                        @endif
                        <div class="card-body">
                            <p class="card-text">{{ $article->title }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="btn-group">
                                    <a href="{{ route('web.plugins.ibkb.articles.show', $article->id) }}" type="button"
                                        class="btn btn-sm btn-outline-secondary">Подробнее</a>
                                </div>
                                <small class="text-muted">{{ $article->created_at->format('d.m.Y H:i') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

    <style>
        .card-image-container {
            height: 225px;
            width: 100%;
            display: block;
        }

        .card-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }
    </style>
</div>
