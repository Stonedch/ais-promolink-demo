@use ('\App\Plugins\IBKnowledgeBase\Models\Article')

@if (\App\Plugins\IBKnowledgeBase\Providers\IBKnowledgeBaseServiceProvider::isActive())
    <div class="album py-5 bg-light my-5">
        <div class="container">

            <h2 class="title my-0 mb-3">База знаний по ИБ</h2>

            <div class="d-flex gap-1 mb-3 flex-wrap">
                @foreach (array_unique(explode(';', implode(';', Article::pluck('tags')->toArray()))) as $tag)
                    <button type="button" class="btn btn-secondary"
                        style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                        {{ $tag }}
                    </button>
                @endforeach
            </div>

            <div class="row">
                @foreach (Article::whereNull('parent_id')->with(['pictures'])->take(9)->get() as $article)
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
@endif
