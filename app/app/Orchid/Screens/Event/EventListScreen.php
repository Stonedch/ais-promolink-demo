<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Event;

use App\Exceptions\HumanException;
use App\Helpers\FormHelper;
use App\Models\Departament;
use App\Models\DepartamentType;
use App\Models\District;
use App\Models\Event;
use App\Models\Form;
use App\Orchid\Components\DateTimeRender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Throwable;

class EventListScreen extends Screen
{
    public $events;
    public $forms;
    public $departaments;

    public function query(): iterable
    {
        $events = Event::filters()->defaultSort('id', 'desc')->paginate();
        $forms = Form::whereIn('id', $events->pluck('form_id'))->get();
        $departaments = Departament::whereIn('id', $events->pluck('departament_id'))->get();

        return [
            'events' => $events,
            'forms' => $forms,
            'departaments' => $departaments,
        ];
    }

    public function name(): ?string
    {
        return 'События';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.events.list',
        ];
    }

    public function commandBar(): iterable
    {
        return [
            Button::make('Удалить страницу выборки')
                ->icon('bs.trash')
                ->method('removePage')
                ->confirm('Выборка отчетов будет удалена'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::block([
                Layout::tabs([
                    'По типу учреждения' => [Layout::rows([
                        Group::make([
                            Select::make('events.departament_type_id')
                                ->empty('-')
                                ->options(fn() => DepartamentType::pluck('name', 'id'))
                                ->title('Тип учреждения'),
                            Select::make('events.form_id')
                                ->empty('-')
                                ->options(function () {
                                    return Form::where('periodicity', 50)->pluck('name', 'id');
                                })
                                ->title('Формы'),
                        ]),
                        Button::make('Создать')
                            ->icon('bs.check-circle')
                            ->canSee(Auth::user()->hasAccess('platform.events.create'))
                            ->method('createEvents')
                    ])],
                    'По району' => [Layout::rows([
                        Group::make([
                            Select::make('eventsByDistrict.district_id')
                                ->empty('-')
                                ->options(fn() => District::pluck('name', 'id'))
                                ->title('Районы'),
                            Select::make('eventsByDistrict.form_id')
                                ->empty('-')
                                ->options(function () {
                                    return Form::where('periodicity', 50)->pluck('name', 'id');
                                })
                                ->title('Формы'),
                        ]),
                        Button::make('Создать')
                            ->icon('bs.check-circle')
                            ->canSee(Auth::user()->hasAccess('platform.events.create'))
                            ->method('createEventsByDistrict')
                    ])],
                    'По учреждению' => [Layout::rows([
                        Group::make([
                            Select::make('eventsByDistrict.departament_id')
                                ->empty('-')
                                ->options(fn() => Departament::pluck('name', 'id'))
                                ->title('Учреждения'),
                            Select::make('eventsByDistrict.form_id')
                                ->empty('-')
                                ->options(function () {
                                    return Form::where('periodicity', 50)->pluck('name', 'id');
                                })
                                ->title('Формы'),
                        ]),
                        Button::make('Создать')
                            ->icon('bs.check-circle')
                            ->canSee(Auth::user()->hasAccess('platform.events.create'))
                            ->method('createEventsByDepartament')
                    ])],
                ]),
                Layout::rows([]),
            ])->title('Создание событий'),

            Layout::table('events', [
                TD::make(__('Actions'))
                    ->align(TD::ALIGN_CENTER)
                    ->width(100)
                    ->canSee(Auth::user()->hasAccess('platform.form_results.list') || Auth::user()->hasAccess('platform.events.edit'))
                    ->render(fn(Event $event) => DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            Link::make('Результаты')
                                ->route('platform.events.results', $event->id)
                                ->icon('bs.back')
                                ->canSee(Auth::user()->hasAccess('platform.form_results.list')),

                            Button::make(__('Delete'))
                                ->icon('bs.trash3')
                                ->confirm('Элемент будет удален')
                                ->method('remove', [
                                    'id' => $event->id,
                                ])
                                ->canSee(Auth::user()->hasAccess('platform.events.edit')),
                        ])),

                TD::make('id', '#')
                    ->filter(TD::FILTER_NUMERIC)
                    ->sort()
                    ->defaultHidden()
                    ->width(100),

                TD::make('form_id', 'Форма')
                    ->sort()
                    ->width(200)
                    ->filter(TD::FILTER_SELECT, Form::pluck('name', 'id'))
                    ->render(function (Event $event) {
                        try {
                            $form = $this->forms->find($event->form_id);
                            return "[#$form->id] $form->name";
                        } catch (Throwable $e) {
                            return '-';
                        }
                    }),

                TD::make('departament_id', 'Учреждение')
                    ->sort()
                    ->width(200)
                    ->filter(TD::FILTER_SELECT, Departament::pluck('name', 'id'))
                    ->render(function (Event $event) {
                        try {
                            $departament = $this->departaments->find($event->departament_id);
                            return "[#$departament->id] $departament->name";
                        } catch (Throwable $e) {
                            return '-';
                        }
                    }),

                TD::make('', 'Процент заполнения')
                    ->width(200)
                    ->render(fn(Event $event) => FormHelper::getPercent($event) . '%'),

                TD::make('filled_at', 'Дата заполнения')
                    ->usingComponent(DateTimeRender::class)
                    ->sort()
                    ->width(200),

                TD::make('filled_at', 'Дата обновления заполнения')
                    ->usingComponent(DateTimeRender::class)
                    ->sort()
                    ->width(200),

                TD::make('created_at', 'Создано')
                    ->usingComponent(DateTimeRender::class)
                    ->filter(TD::FILTER_DATE_RANGE)
                    ->sort()
                    ->width(200),

                TD::make('updated_at', 'Обновлено')
                    ->usingComponent(DateTimeRender::class)
                    ->filter(TD::FILTER_DATE_RANGE)
                    ->sort()
                    ->width(200),
            ])->title('Лог событий'),
        ];
    }

    public function createEvents(Request $request)
    {
        try {
            $departamentTypeId = $request->input('events.departament_type_id', null);
            $formId = $request->input('events.form_id', null);

            throw_if(empty($departamentTypeId), new HumanException('Пожалуйста, укажите ведомства!'));
            throw_if(empty($formId), new HumanException('Пожалуйста, укажите форму!'));

            $form = Form::find($formId);
            $departamentType = DepartamentType::find($departamentTypeId);

            throw_if(empty($form), new HumanException('Форма не найдена!'));
            throw_if(empty($departamentType), new HumanException('Ведомство не найдено!'));

            Event::createBy($form, $departamentType);

            Toast::success('Успешно');
        } catch (HumanException $e) {
            Toast::error($e->getMessage());
        } catch (Throwable $e) {
            Toast::error("Внутренняя ошибка! {$e->getMessage()}");
        }
    }

    public function createEventsByDistrict(Request $request)
    {
        try {
            $districtId = $request->input('eventsByDistrict.district_id');
            $formId = $request->input('eventsByDistrict.form_id');

            throw_if(empty($districtId), new HumanException('Пожалуйста, укажите район!'));
            throw_if(empty($formId), new HumanException('Пожалуйста, укажите форму!'));

            $district = District::find($districtId);
            $form = Form::find($formId);

            throw_if(empty($district), new HumanException('Район не найден!'));
            throw_if(empty($form), new HumanException('Форма не найдена!'));

            Departament::query()
                ->where('district_id', $district->id)
                ->get()
                ->map(fn(Departament $departament) => Event::createByDistrict($form, $departament));

            Toast::success('Успешно');
        } catch (HumanException $e) {
            Toast::error($e->getMessage());
        } catch (Throwable $e) {
            Toast::error("Внутренняя ошибка! {$e->getMessage()}");
        }
    }

    public function createEventsByDepartament(Request $request)
    {
        try {
            $departamentIdentifier = $request->input('eventsByDistrict.departament_id');
            $formIdentifier = $request->input('eventsByDistrict.form_id');

            throw_if(empty($departamentIdentifier), new HumanException('Пожалуйста, укажите район!'));
            throw_if(empty($formIdentifier), new HumanException('Пожалуйста, укажите форму!'));

            $departament = Departament::find($departamentIdentifier);
            $form = Form::find($formIdentifier);

            throw_if(empty($departament), new HumanException('Район не найден!'));
            throw_if(empty($form), new HumanException('Форма не найдена!'));

            Event::createByDistrict($form, $departament);

            Toast::success('Успешно');
        } catch (HumanException $e) {
            Toast::error($e->getMessage());
        } catch (Throwable $e) {
            Toast::error("Внутренняя ошибка! {$e->getMessage()}");
        }
    }

    public function remove(Request $request): void
    {
        Event::findOrFail($request->input('id'))->delete();
        Toast::info('Успешно удалено!');
    }

    public function removePage(Request $request): void
    {
        $this->events->map(function (Event $event) {
            $event->delete();
        });

        Toast::success("Страница была удалена!");
    }
}
