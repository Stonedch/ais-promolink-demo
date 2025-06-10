<?php

declare(strict_types=1);

use App\Helpers\PhoneNormalizer;
use App\Http\Middleware\UserUnActive;
use App\Orchid\Screens\BotNotification\BotNotificationScreen;
use App\Orchid\Screens\BotUserQuestion\BotUserQuestionListScreen;
use App\Orchid\Screens\Collection\CollectionEditScreen;
use App\Orchid\Screens\Collection\CollectionListScreen;
use App\Orchid\Screens\CustomReportLog\CustomReporByDepartamentLogListScreen;
use App\Orchid\Screens\CustomReportLog\CustomReportLogListScreen;
use App\Orchid\Screens\CustomReportType\CustomReportTypeEditScreen;
use App\Orchid\Screens\CustomReportType\CustomReportTypeListScreen;
use App\Orchid\Screens\Departament\DepartamentListScreen;
use App\Orchid\Screens\Departament\DepartamentEditScreen;
use App\Orchid\Screens\DepartamentType\DepartamentTypeEditScreen;
use App\Orchid\Screens\DepartamentType\DepartamentTypeListScreen;
use App\Orchid\Screens\District\DistrictEditScreen;
use App\Orchid\Screens\District\DistrictListScreen;
use App\Orchid\Screens\Event\EventListScreen;
use App\Orchid\Screens\Event\ResultListScreen;
use App\Orchid\Screens\ExternalDepartament\ExternalDepartamentEditScreen;
use App\Orchid\Screens\ExternalDepartament\ExternalDepartamentListScreen;
use App\Orchid\Screens\Form\FormEditScreen;
use App\Orchid\Screens\Form\FormListScreen;
use App\Orchid\Screens\FormCategory\FormCategoryEditScreen;
use App\Orchid\Screens\FormCategory\FormCategoryListScreen;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\Subdepartament\DepartamentEditScreen as SubdepartamentDepartamentEditScreen;
use App\Orchid\Screens\Subdepartament\DepartamentListScreen as SubdepartamentDepartamentListScreen;
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
    ->middleware([UserUnActive::class])
    ->name('platform.main');

// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.profile')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Profile'), route('platform.profile')));

// Platform > System > Users > User
Route::screen('users/{user}/edit', UserEditScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.systems.users.edit')
    ->breadcrumbs(fn(Trail $trail, $user) => $trail
        ->parent('platform.systems.users')
        ->push(PhoneNormalizer::humanizePhone($user->phone), route('platform.systems.users.edit', $user)));

// Platform > System > Users > Create
Route::screen('users/create', UserEditScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.systems.users.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.systems.users')
        ->push(__('Create'), route('platform.systems.users.create')));

// Platform > System > Users
Route::screen('users', UserListScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.systems.users')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Users'), route('platform.systems.users')));

// Platform > System > Roles > Role
Route::screen('roles/{role}/edit', RoleEditScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(fn(Trail $trail, $role) => $trail
        ->parent('platform.systems.roles')
        ->push($role->name, route('platform.systems.roles.edit', $role)));

// Platform > System > Roles > Create
Route::screen('roles/create', RoleEditScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.systems.roles.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.systems.roles')
        ->push(__('Create'), route('platform.systems.roles.create')));

// Platform > System > Roles
Route::screen('roles', RoleListScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.systems.roles')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Roles'), route('platform.systems.roles')));

// Platform > Collections 
Route::screen('collections', CollectionListScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.collections')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Коллекции', route('platform.collections')));

// Platform > Collections > Create
Route::screen('collections/create', CollectionEditScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.collections.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.collections')
        ->push('Создание', route('platform.collections.create')));

// Platform > Collections > Edit
Route::screen('collections/{collection}/edit', CollectionEditScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.collections.edit')
    ->breadcrumbs(fn(Trail $trail, $collection) => $trail
        ->parent('platform.collections')
        ->push('Редактирование', route('platform.collections.edit', $collection)));

// Platform > DepartamentTypes
Route::screen('departament-types', DepartamentTypeListScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.departament-types')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Типы Учреждений', route('platform.departament-types')));

// Platform > DepartamentTypes > Create
Route::screen('departament-types/create', DepartamentTypeEditScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.departament-types.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.departament-types')
        ->push('Создание', route('platform.departament-types.create')));

// Platform > DepartamentTypes > Edit
Route::screen('departament-types/{departamentType}/edit', DepartamentTypeEditScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.departament-types.edit')
    ->breadcrumbs(fn(Trail $trail, $departamentType) => $trail
        ->parent('platform.departament-types')
        ->push('Редактирование', route('platform.departament-types.edit', $departamentType)));

// Platform > Departaments
Route::screen('departaments', DepartamentListScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.departaments')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Учреждения', route('platform.departaments')));

// Platform > Departaments > Create
Route::screen('departaments/create', DepartamentEditScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.departaments.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.departaments')
        ->push('Создание', route('platform.departaments.create')));

// Platform > Departaments > Edit
Route::screen('departaments/{departament}/edit', DepartamentEditScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.departaments.edit')
    ->breadcrumbs(fn(Trail $trail, $departament) => $trail
        ->parent('platform.departaments')
        ->push('Редактирование', route('platform.departaments.edit', $departament)));

// Platform > Forms
Route::screen('forms', FormListScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.forms')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Формы', route('platform.forms')));

// Platform > Forms > Create
Route::screen('forms/create', FormEditScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.forms.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.forms')
        ->push('Создание', route('platform.forms.create')));

// Platform > Forms > Edit
Route::screen('forms/{form}/edit', FormEditScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.forms.edit')
    ->breadcrumbs(fn(Trail $trail, $form) => $trail
        ->parent('platform.forms')
        ->push('Редактирование', route('platform.forms.edit', $form)));

// Platform > FormCategories
Route::screen('form-categories', FormCategoryListScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.form-categories')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Категории форм', route('platform.form-categories')));

// Platform > FormCategories > Create
Route::screen('form-categiries/create', FormCategoryEditScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.form-categories.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.form-categories')
        ->push('Создание', route('platform.form-categories.create')));

// Platform > FormCategories > Edit
Route::screen('form-categories/{formCategory}/edit', FormCategoryEditScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.form-categories.edit')
    ->breadcrumbs(fn(Trail $trail, $formCategory) => $trail
        ->parent('platform.form-categories')
        ->push('Редактирование', route('platform.form-categories.edit', $formCategory)));

// Platform > Events
Route::screen('events', EventListScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.events')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push('События', route('platform.events')));

// Platform > Events > Results
Route::screen('events/{event}/results', ResultListScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.events.results')
    ->breadcrumbs(fn(Trail $trail, $event) => $trail
        ->parent('platform.index')
        ->push('Результаты', route('platform.events.results', $event)));

// Platform > Districts
Route::screen('districts', DistrictListScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.districts')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Районы', route('platform.districts')));

// Platform > Districts > Create
Route::screen('districts/create', DistrictEditScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.districts.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.districts')
        ->push('Создание', route('platform.districts.create')));

// Platform > Districts > Edit
Route::screen('districts/{district}/edit', DistrictEditScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.districts.edit')
    ->breadcrumbs(fn(Trail $trail, $district) => $trail
        ->parent('platform.districts')
        ->push('Редактирование', route('platform.districts.edit', $district)));

// Platform > ExternalDepartaments
Route::screen('external-departaments', ExternalDepartamentListScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.external-departaments')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Внешние учреждения', route('platform.external-departaments')));

// Platform > ExternalDepartaments > Create
Route::screen('external-departaments/create', ExternalDepartamentEditScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.external-departaments.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.external-departaments')
        ->push('Создание', route('platform.external-departaments.create')));

// Platform > ExternalDepartaments > Edit
Route::screen('external-departaments/{externalDepartament}/edit', ExternalDepartamentEditScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.external-departaments.edit')
    ->breadcrumbs(fn(Trail $trail, $externalDepartament) => $trail
        ->parent('platform.external-departaments')
        ->push('Редактирование', route('platform.external-departaments.edit', $externalDepartament)));

// Platform > CustomReportTypes
Route::screen('custom-report-types', CustomReportTypeListScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.custom-report-types')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Типы загружаемых документов', route('platform.custom-report-types')));

// Platform > CustomReportTypes > Create
Route::screen('custom-report-types/create', CustomReportTypeEditScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.custom-report-types.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.custom-report-types')
        ->push('Создание', route('platform.custom-report-types.create')));

// Platform > CustomReportTypes > Edit
Route::screen('custom-report-types/{customReportType}/edit', CustomReportTypeEditScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.custom-report-types.edit')
    ->breadcrumbs(fn(Trail $trail, $customReportType) => $trail
        ->parent('platform.custom-report-types')
        ->push('Редактирование', route('platform.custom-report-types.edit', $customReportType)));

// Platform > BotNotifications
Route::screen('bot-notifications', BotNotificationScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.bot-notifications')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Бот-уведомления', route('platform.bot-notifications')));

// Platform > CustomReportLogs
Route::screen('custom-report-logs', CustomReportLogListScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.custom-report-logs')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Лог загружаемых документов', route('platform.custom-report-logs')));

// Platform > CustomReportLogs > 
Route::screen('custom-report-logs/by-departaments', CustomReporByDepartamentLogListScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.custom-report-logs.by-departaments')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.custom-report-logs')
        ->push('По учреждениям', route('platform.custom-report-logs.by-departaments')));

Route::get('/custom-report-logs/by-departaments/download/{id}', [CustomReporByDepartamentLogListScreen::class, 'download'])
    ->name('platform.custom-report-logs.by-departaments.download')
    ->middleware(['web', 'auth']);

// Platform > BotUserQuestions
Route::screen('bot-user-questions', BotUserQuestionListScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.bot-user-questions')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Вопросы бот-пользователей', route('platform.bot-user-questions')));

// Platform > Subdepartaments > Departaments
Route::screen('subdepartaments/departaments', SubdepartamentDepartamentListScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.subdepartaments.departaments')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Подведомства', route('platform.subdepartaments.departaments')));

// Platform > Subdepartaments > Departaments > Create
Route::screen('subdepartaments/departaments/create', SubdepartamentDepartamentEditScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.subdepartaments.departaments.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.subdepartaments.departaments')
        ->push('Создание', route('platform.subdepartaments.departaments.create')));

// Platform > Departaments > Edit
Route::screen('subdepartaments/departaments/{departament}/edit', SubdepartamentDepartamentEditScreen::class)
    ->middleware([UserUnActive::class])
    ->name('platform.subdepartaments.departaments.edit')
    ->breadcrumbs(fn(Trail $trail, $departament) => $trail
        ->parent('platform.subdepartaments.departaments')
        ->push('Редактирование', route('platform.subdepartaments.departaments.edit', $departament)));
