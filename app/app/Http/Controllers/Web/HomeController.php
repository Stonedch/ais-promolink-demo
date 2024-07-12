<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\HumanException;
use App\Helpers\FormHelper;
use App\Http\Controllers\Controller;
use App\Models\Departament;
use App\Models\Event;
use App\Models\Form;
use App\Models\FormCategory;
use App\Models\FormDepartamentType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Throwable;

class HomeController extends Controller
{
    public static array $views = [
        'index' => 'web.home.index',
    ];

    public function index(): View|RedirectResponse
    {
        try {
            $user = Auth::user();

            $this->checkAccess($user);

            if ($user->hasAnyAccess(['platform.min.base'])) {
                return $this->indexMinister();
            } elseif ($user->hasAnyAccess(['platform.supervisor.base'])) {
                return $this->indexSupervisor();
            } else {
                return $this->indexUser();
            }
        } catch (HumanException $e) {
            return redirect()
                ->route('web.index.index')
                ->withErrors([$e->getMessage()]);
        } catch (Throwable $e) {
            dd($e);
            abort(500);
        }
    }

    private function indexMinister(): RedirectResponse
    {
        return redirect()->route('web.minister.index');
    }

    private function indexSupervisor(): View
    {
        $response = FormHelper::byDepartaments(
            Departament::whereNotNull('departament_type_id')->get()
        );

        return view(self::$views['index'], $response);
    }

    private function indexUser(User $user = null): View
    {
        if (empty($user)) $user = Auth::user();

        $response = [];

        $response['user'] = $user;
        $response['notification'] = $user->notifications()->get();
        $response['departament'] = $user->getDepartament();

        throw_if(empty($response['departament']), new HumanException('Ошибка проверки пользователя!'));
        throw_if(empty($response['departament']), new HumanException('Ошибка проверки ведомства!'));

        $response['allEvents'] = Event::where('departament_id', $response['departament']->id)->get();
        $response['events'] = $response['allEvents']->where('filled_at', null);
        $response['writedEvents'] = $response['allEvents']->where('filled_at', '!=', null)->keyBy('id')->groupBy('form_id', true);

        $response['forms'] = Form::query()
            ->where('is_active', true)
            ->where(function (Builder $query) use ($response) {
                $formIdentifiers = FormDepartamentType::query()
                    ->where('departament_type_id', $response['departament']->departament_type_id)
                    ->pluck('form_id')
                    ->toArray();

                $formIdentifiers = array_merge($formIdentifiers, $response['events']->pluck('form_id')->toArray());
                $formIdentifiers = collect($formIdentifiers)->unique();

                $query->whereIn('id', $formIdentifiers);
            })
            ->get();

        $response['deadlines'] = new Collection();
        $response['difs'] = new Collection();

        $response['events']->map(function (Event $event) use (&$response) {
            $deadline = $response['forms']->where('id', $event->form_id)->first()->deadline;
            $deadline = empty($deadline) == false
                ? intval(now()->diff((new Carbon($event->created_at))->addDays($deadline))->format('%d'))
                : null;
            $response['deadlines']->put($event->id, $deadline);
            $response['difs']->put($event->id, now()->diffInSeconds((new Carbon($event->created_at))->addDays($deadline)));
        });

        $response['formCategories'] = FormCategory::query()
            ->whereIn('id', $response['forms']->pluck('form_category_id'))
            ->get()
            ->keyBy('id');

        $response['formCategoryCounters'] = [];

        $response['allEvents']->map(function (Event $event) use (&$response) {
            try {
                $formCategoryIdentifier = $response['forms']->where('id', $event->form_id)->first()->form_category_id;
                $currentEventStatus = $event->getCurrentStatus();

                if (isset($response['formCategoryCounters'][$formCategoryIdentifier]) == false) {
                    $response['formCategoryCounters'][$formCategoryIdentifier] = [];

                    foreach (Event::$STATUSES as $key => $status) {
                        $response['formCategoryCounters'][$formCategoryIdentifier][$key] = 0;
                    }
                }

                if (isset($response['formCategoryCounters'][$formCategoryIdentifier][$currentEventStatus]) == false) {
                    $response['formCategoryCounters'][$formCategoryIdentifier][$currentEventStatus] = 0;
                }

                $response['formCategoryCounters'][$formCategoryIdentifier][$currentEventStatus] += 1;
            } catch (Throwable) {
            }
        });

        return view(self::$views['index'], $response);
    }

    private function checkAccess(User $user = null): void
    {
        if (empty($user)) $user = Auth::user();
        throw_if(empty($user), new HumanException('Ошибка авторизации!'));
    }
}
