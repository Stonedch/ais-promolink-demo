<?php

declare(strict_types=1);

namespace App\Orchid\Screens\FormCategory;

use App\Exceptions\HumanException;
use App\Models\FormCategory;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Throwable;

class FormCategoryEditScreen extends Screen
{
    public ?FormCategory $formCategory = null;

    public function query(FormCategory $formCategory): iterable
    {
        return [
            'formCategory' => $formCategory,
        ];
    }

    public function name(): ?string
    {
        return 'Управление категориями форм';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.form-categories.edit',
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
                ->canSee($this->formCategory->exists),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('formCategory.name')
                    ->required()
                    ->title('Название'),
            ]),
        ];
    }

    public function save(Request $request, FormCategory $formCategory)
    {
        try {
            $formCategory->fill($request->input('formCategory', []));
            $formCategory->save();
            Toast::info('Успешно сохранено!');
            return redirect()->route('platform.form-categories.edit', $formCategory);
        } catch (HumanException $e) {
            Toast::error($e->getMessage());
        } catch (Throwable) {
            Toast::error('Ошибка сервера');
        }
    }

    public function remove(FormCategory $formCategory)
    {
        try {
            $formCategory->delete();
            Toast::info('Успешно удалено');
            return redirect()->route('platform.form-categories');
        } catch (HumanException $e) {
            Toast::error($e->getMessage());
        } catch (Throwable) {
            Toast::error('Ошибка сервера');
        }
    }
}
