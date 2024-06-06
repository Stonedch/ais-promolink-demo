<?php

declare(strict_types=1);

use App\Helpers\PhoneNormalizer;
use App\Orchid\Screens\Collection\CollectionEditScreen;
use App\Orchid\Screens\Collection\CollectionListScreen;
use App\Orchid\Screens\Departament\DepartamentListScreen;
use App\Orchid\Screens\Departament\DepartamentEditScreen;
use App\Orchid\Screens\DepartamentType\DepartamentTypeEditScreen;
use App\Orchid\Screens\DepartamentType\DepartamentTypeListScreen;
use App\Orchid\Screens\District\DistrictEditScreen;
use App\Orchid\Screens\District\DistrictListScreen;
use App\Orchid\Screens\Event\EventListScreen;
use App\Orchid\Screens\Event\ResultListScreen;
use App\Orchid\Screens\Form\FormEditScreen;
use App\Orchid\Screens\Form\FormListScreen;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/

// Main
Route::screen('/main', PlatformScreen::class)
    ->name('platform.main');

// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Profile'), route('platform.profile')));

// Platform > System > Users > User
Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit')
    ->breadcrumbs(fn (Trail $trail, $user) => $trail
        ->parent('platform.systems.users')
        ->push(PhoneNormalizer::humanizePhone($user->phone), route('platform.systems.users.edit', $user)));

// Platform > System > Users > Create
Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.users')
        ->push(__('Create'), route('platform.systems.users.create')));

// Platform > System > Users
Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Users'), route('platform.systems.users')));

// Platform > System > Roles > Role
Route::screen('roles/{role}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(fn (Trail $trail, $role) => $trail
        ->parent('platform.systems.roles')
        ->push($role->name, route('platform.systems.roles.edit', $role)));

// Platform > System > Roles > Create
Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.roles')
        ->push(__('Create'), route('platform.systems.roles.create')));

// Platform > System > Roles
Route::screen('roles', RoleListScreen::class)
    ->name('platform.systems.roles')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Roles'), route('platform.systems.roles')));

// Platform > Collections 
Route::screen('collections', CollectionListScreen::class)
    ->name('platform.collections')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Коллекции', route('platform.collections')));

// Platform > Collections > Create
Route::screen('collections/create', CollectionEditScreen::class)
    ->name('platform.collections.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.collections')
        ->push('Создание', route('platform.collections.create')));

// Platform > Collections > Edit
Route::screen('collections/{collection}/edit', CollectionEditScreen::class)
    ->name('platform.collections.edit')
    ->breadcrumbs(fn (Trail $trail, $collection) => $trail
        ->parent('platform.collections')
        ->push('Редактирование', route('platform.collections.edit', $collection)));

// Platform > DepartamentTypes
Route::screen('departament-types', DepartamentTypeListScreen::class)
    ->name('platform.departament-types')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Типы ведомств', route('platform.departament-types')));

// Platform > DepartamentTypes > Create
Route::screen('departament-types/create', DepartamentTypeEditScreen::class)
    ->name('platform.departament-types.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.departament-types')
        ->push('Создание', route('platform.departament-types.create')));

// Platform > DepartamentTypes > Edit
Route::screen('departament-types/{departamentType}/edit', DepartamentTypeEditScreen::class)
    ->name('platform.departament-types.edit')
    ->breadcrumbs(fn (Trail $trail, $departamentType) => $trail
        ->parent('platform.departament-types')
        ->push('Редактирование', route('platform.departament-types.edit', $departamentType)));

// Platform > Departaments
Route::screen('departaments', DepartamentListScreen::class)
    ->name('platform.departaments')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Ведомства', route('platform.departaments')));

// Platform > Departaments > Create
Route::screen('departaments/create', DepartamentEditScreen::class)
    ->name('platform.departaments.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.departaments')
        ->push('Создание', route('platform.departaments.create')));

// Platform > Departaments > Edit
Route::screen('departaments/{departament}/edit', DepartamentEditScreen::class)
    ->name('platform.departaments.edit')
    ->breadcrumbs(fn (Trail $trail, $departament) => $trail
        ->parent('platform.departaments')
        ->push('Редактирование', route('platform.departaments.edit', $departament)));

// Platform > Forms
Route::screen('forms', FormListScreen::class)
    ->name('platform.forms')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Формы', route('platform.forms')));

// Platform > Forms > Create
Route::screen('forms/create', FormEditScreen::class)
    ->name('platform.forms.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.forms')
        ->push('Создание', route('platform.forms.create')));

// Platform > Forms > Edit
Route::screen('forms/{form}/edit', FormEditScreen::class)
    ->name('platform.forms.edit')
    ->breadcrumbs(fn (Trail $trail, $form) => $trail
        ->parent('platform.forms')
        ->push('Редактирование', route('platform.forms.edit', $form)));

// Platform > Events
Route::screen('events', EventListScreen::class)
    ->name('platform.events')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('События', route('platform.events')));

// Platform > Events > Results
Route::screen('events/{event}/results', ResultListScreen::class)
    ->name('platform.events.results')
    ->breadcrumbs(fn (Trail $trail, $event) => $trail
        ->parent('platform.index')
        ->push('Результаты', route('platform.events.results', $event)));

// Platform > Districts
Route::screen('districts', DistrictListScreen::class)
    ->name('platform.districts')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Районы', route('platform.districts')));

// Platform > Districts > Create
Route::screen('districts/create', DistrictEditScreen::class)
    ->name('platform.districts.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.districts')
        ->push('Создание', route('platform.districts.create')));

// Platform > Districts > Edit
Route::screen('districts/{district}/edit', DistrictEditScreen::class)
    ->name('platform.districts.edit')
    ->breadcrumbs(fn (Trail $trail, $district) => $trail
        ->parent('platform.districts')
        ->push('Редактирование', route('platform.districts.edit', $district)));
