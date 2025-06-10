<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Subdepartament;

use App\Helpers\PhoneNormalizer;
use App\Models\Departament;
use App\Models\User;
use App\Orchid\Layouts\Role\RolePermissionLayout;
use App\Orchid\Layouts\User\UserEditLayout;
use App\Orchid\Layouts\User\UserPasswordLayout;
use App\Orchid\Layouts\User\UserRoleLayout;
use Exception;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Orchid\Access\Impersonation;
use Orchid\Platform\Models\Role;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Cropper;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Password;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Throwable;

class UserEditScreen extends Screen
{
    public $user;

    public function query(User $user): iterable
    {
        $user->load(['roles']);

        return [
            'user' => $user,
            'permission' => $user->getStatusPermission(),
        ];
    }

    public function name(): ?string
    {
        return $this->user->exists ? 'Edit User' : 'Create User';
    }

    public function description(): ?string
    {
        return 'User profile and privileges, including their associated role.';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.subdepartaments.base',
        ];
    }

    public function commandBar(): iterable
    {
        return [
            Button::make(__('Remove'))
                ->icon('bs.trash3')
                ->confirm(__('Once the account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.'))
                ->method('remove')
                ->canSee($this->user->exists),

            Button::make(__('Save'))
                ->icon('bs.check-circle')
                ->method('save'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::block(Layout::rows([
                Input::make('user.departament_id')
                    ->value(request()->input('parent_id', request()->user()->departament_id))
                    ->hidden(),

                Input::make('user.phone')
                    ->type('text')
                    ->required()
                    ->title('Номер телефона'),

                Input::make('user.email')
                    ->type('text')
                    ->max(255)
                    ->title('E-mail'),

                Input::make('user.last_name')
                    ->type('text')
                    ->max(255)
                    ->title('Фамилия'),

                Input::make('user.first_name')
                    ->type('text')
                    ->max(255)
                    ->title('Имя'),

                Input::make('user.middle_name')
                    ->type('text')
                    ->max(255)
                    ->title('Отчество'),

                Cropper::make('user.attachment_id')
                    ->targetId()
                    ->title('Аватар'),

                CheckBox::make('user.is_active')
                    ->value(true)
                    ->sendTrueOrFalse()
                    ->title('Активность пользователя'),
            ]))
                ->title(__('Profile Information'))
                ->description(__('Update your account\'s profile information and email address.'))
                ->commands(
                    Button::make(__('Save'))
                        ->type(Color::BASIC)
                        ->icon('bs.check-circle')
                        ->canSee($this->user->exists)
                        ->method('save')
                ),

            Layout::block(Layout::rows([
                Password::make('user.password')
                    ->placeholder($this->user->exists ? __('Leave empty to keep current password') : __('Enter the password to be set'))
                    ->title(__('Password')),
            ]))
                ->title(__('Password'))
                ->description(__('Ensure your account is using a long, random password to stay secure.'))
                ->commands(
                    Button::make(__('Save'))
                        ->type(Color::BASIC)
                        ->icon('bs.check-circle')
                        ->canSee($this->user->exists)
                        ->method('save')
                ),
        ];
    }

    public function save(User $user, Request $request)
    {
        $request->merge([
            'user' => [
                ...$request->input('user'),
                'phone' => PhoneNormalizer::normalizePhone($request->input('user.phone')),
            ],
        ]);

        if ($user->exists) {
            $request->validate([
                'user.phone' => "required|unique:users,phone,{$user->id}",
            ], [
                'user.phone.required' => 'Поле "Номер телефона" обязательно к заполнению',
                'user.phone.unique' => 'Поле "Номер телефона" должно быть уникальным',
                'user.email.unique' => 'Поле "E-mail" должно быть уникальным',
            ]);
        } else {
            $request->validate([
                'user.phone' => 'required|unique:users,phone',
                'user.password' => 'required',
            ], [
                'user.phone.required' => 'Поле "Номер телефона" обязательно к заполнению',
                'user.phone.unique' => 'Поле "Номер телефона" должно быть уникальным',
                'user.password.required' => 'Поле "Пароль" обязательно к заполнению',
                'user.email.unique' => 'Поле "E-mail" должно быть уникальным',
            ]);
        }

        $user->when($request->filled('user.password'), function (Builder $builder) use ($request) {
            $builder->getModel()->password = Hash::make($request->input('user.password'));
        });

        $user
            ->fill($request->collect('user')->except(['password', 'permissions', 'roles'])->toArray())
            ->save();

        Role::query()
            ->whereIn('slug', ['departament-worker-cr', 'departament-worker'])
            ->get()
            ->map(function (Role $role) use ($user) {
                if ($user->inRole($role) == false) {
                    $user->addRole($role);
                }
            });

        Toast::info('Пользователь сохранен');

        return redirect()->route('platform.subdepartaments.users.edit', [$user->id]);
    }

    public function remove(User $user)
    {
        $user->delete();

        Toast::info(__('User was removed'));

        return redirect()->route('platform.systems.users');
    }
}
