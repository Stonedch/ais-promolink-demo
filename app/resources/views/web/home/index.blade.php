@extends('web.layouts.layout')

@section('content')
    <x-breadcrumbs.list>
        <x-breadcrumbs.item current="true" href="/" label="Главная">Главная</x-breadcrumbs.item>
    </x-breadcrumbs.list>

    <x-notifications.list />

    <x-accordion.container title="Отчеты">
        @foreach (collect($formCategories)->merge([new \App\Models\FormCategory()]) as $i => $category)
            <x-accordion.item :title="@$category['name'] ?: 'Вне группы'" :show="true">
                <x-tables.base-forms :forms="$forms->where('form_category_id', @$category['id'])" />
            </x-accordion.item>
        @endforeach
    </x-accordion.container>

    @if ($onApproved?->count())
        <x-accordion.container title="Отчеты на проверку">
            @foreach ($onApproved->groupBy('departament_id') as $departamentId => $events)
                @php
                    $approvedForms = [];
                    foreach ($events as $event) {
                        $approvedForm = $event->form;
                        $approvedForm->event = $event;
                        $approvedForms[] = $approvedForm;
                    }
                    $approvedForms = collect($approvedForms);
                @endphp
                <x-accordion.item :title="'Учреждение: ' . $events->first()->departament->name" :show="true">
                    <x-tables.base-forms :forms="$approvedForms" :checking="false" />
                </x-accordion.item>
            @endforeach
        </x-accordion.container>
    @endif

    @if (\App\Plugins\IBKnowledgeBase\Providers\IBKnowledgeBaseServiceProvider::isActive())
        @include('IBKnowledgeBase::articles.list')
    @endif
@endsection
