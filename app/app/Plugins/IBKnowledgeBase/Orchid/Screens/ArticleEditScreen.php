<?php

namespace App\Plugins\IBKnowledgeBase\Orchid\Screens;

use App\Plugins\IBKnowledgeBase\Models\Article;
use App\Plugins\IBKnowledgeBase\Models\InformationSystem;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Matrix;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class ArticleEditScreen extends Screen
{
    public ?Article $article = null;

    public function name(): ?string
    {
        return $this->article->exists ? 'Редактирование статьи' : 'Создание статьи';
    }

    public function query(?Article $article): iterable
    {
        return [
            'article' => $article,
        ];
    }

    public function commandBar(): array
    {
        return [
            Button::make('Сохранить')
                ->icon('check')
                ->method('save'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('article.title')
                    ->title('Название статьи')
                    ->required(),

                Quill::make('article.content')
                    ->title('Содержание')
                    ->toolbar(["text", "color", "header", "list", "format", "media"])
                    ->required(),

                Relation::make('article.parent_id')
                    ->title('Родительская статья')
                    ->fromModel(Article::class, 'title'),

                Upload::make('article.attachment')
                    ->title('Прикрепленные файлы')
                    ->groups('ibkb_attachments'),

                Matrix::make('article.tags')
                    ->value(fn(): array => $this->article->exists ? $this->article->getMatrixTags() : [])
                    ->columns(['Тэг' => 'tag'])
                    ->title('Тэги'),
            ]),
        ];
    }

    public function save(Article $article, Request $request)
    {
        $article->fill($request->get('article'));
        $article->author_id = $request->user()->id;
        $article->tags = implode(
            ';',
            collect($request->input('article.tags', []))
                ->pluck('tag')
                ->map(fn(?string $tag): string => trim(mb_strtolower($tag ?: '')))
                ->toArray()
        );
        $article->save();

        $article->attachment()->syncWithoutDetaching(
            $request->input('article.attachment', [])
        );

        Alert::info('Статья сохранена');

        return redirect()->route('platform.ibkb.article.edit', $article->id);
    }
}
