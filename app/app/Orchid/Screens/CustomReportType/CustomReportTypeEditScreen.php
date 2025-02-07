<?php

declare(strict_types=1);

namespace App\Orchid\Screens\CustomReportType;

use App\Models\CustomReportType;
use App\Models\CustomReportTypeUser;
use App\Models\User;
use App\Orchid\Fields\SingleUpload;
use Exception;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class CustomReportTypeEditScreen extends Screen
{
    public $customReportType;

    public function query(CustomReportType $customReportType): iterable
    {
        throw_if(config('app.custom_reports') == false, new Exception('Закрытый доступ!'));

        if ($customReportType->exists) {
            $customReportType->users = $customReportType->getUsers()->pluck('id')->toArray();
        }

        return [
            'customReportType' => $customReportType,
        ];
    }

    public function name(): ?string
    {
        return 'Управление Типом кастомных отчетов';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.custom-reports.base',
        ];
    }

    public function commandBar(): iterable
    {
        return [
            Button::make('Сохранить')
                ->icon('bs.check')
                ->method('save'),

            Button::make('Удалить')
                ->icon('bs.trash')
                ->method('remove')
                ->canSee($this->customReportType->exists),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('customReportType.title')
                    ->required()
                    ->title('Название'),

                Select::make('customReportType.users')
                    ->options(function () {
                        $options = [];

                        User::query()
                            ->select(['id', 'last_name', 'first_name', 'middle_name', 'phone'])
                            ->get()
                            ->map(function (User $user) use (&$options) {
                                $options[$user->id] = "{$user->last_name} {$user->first_name} {$user->middle_name}, +7{$user->phone}";
                            });

                        return $options;
                    })
                    ->title('Пользователи')
                    ->multiple()
                    ->canSee($this->customReportType->exists),

                CheckBox::make('customReportType.is_general')
                    ->sendTrueOrFalse()
                    ->title('Является общим'),

                SingleUpload::make('customReportType.attachment_id')
                    ->storage('private')
                    ->title('Шаблон'),
            ]),
        ];
    }

    public function save(Request $request, CustomReportType $customReportType)
    {
        $request->merge([
            'customReportType' => [
                ...$request->input('customReportType'),
                'attachment_id' => collect($request->input('customReportType.attachment_id', []))->first(),
            ]
            ]);

        $customReportType->fill($request->input('customReportType', []));

        $customReportType->save();

        CustomReportTypeUser::query()
            ->where('custom_report_type_id', $customReportType->id)
            ->get()
            ->map(fn(CustomReportTypeUser $item) => $item->delete());

        foreach ($request->input('customReportType.users', []) as $id) {
            (new CustomReportTypeUser())->fill([
                'custom_report_type_id' => $customReportType->id,
                'user_id' => $id,
            ])->save();
        }

        Toast::info('Успешно сохранено!');

        return redirect()->route('platform.custom-report-types.edit', $customReportType);
    }

    public function remove(CustomReportType $customReportType)
    {
        $customReportType->delete();
        Toast::info('Успешно удалено');
        return redirect()->route('platform.custom-report-types');
    }
}
