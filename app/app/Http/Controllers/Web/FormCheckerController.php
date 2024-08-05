<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\HumanException;
use App\Helpers\Responser;
use App\Http\Controllers\Controller;
use App\Models\Departament;
use App\Models\Event;
use App\Models\Field;
use App\Models\Form;
use App\Models\FormChecker;
use App\Models\FormCheckerResult;
use App\Models\FormCheckerResultStatuses;
use App\Models\FormResult;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class FormCheckerController extends Controller
{
    public static array $views = [
        'index' => 'web.form-checker.index',
    ];

    public function index(): View|RedirectResponse
    {
        try {
            $user = Auth::user();

            $this->checkAccess($user);

            $formCheckers = FormChecker::query()
                ->where('user_id', $user->id)
                ->get();
            $formCheckerResults = FormCheckerResult::query()
                ->where('user_id', $user->id)
                ->get();
            $events = Event::query()
                ->whereIn('form_id', $formCheckers->pluck('form_id'))
                ->get()
                ->map(function (Event $event) use ($formCheckerResults) {
                    $event->form_structure = json_decode($event->form_structure);

                    $formCheckerResultCounts = $formCheckerResults->where('event_id', $event->id)->count();
                    $formCheckerResultNotInProgresses = $formCheckerResults->where('event_id', $event->id)
                        ->where('status', '!=', FormCheckerResultStatuses::IN_PROGRESS->value)->count();


                    if ($formCheckerResultCounts == 0) {
                        $event->form_checker_result_status = 'new';
                    } elseif ($formCheckerResultCounts == $formCheckerResultNotInProgresses) {
                        $event->form_checker_result_status = 'finished';
                    } elseif (0 < $formCheckerResultNotInProgresses) {
                        $event->form_checker_result_status = 'in-progress';
                    } else {
                        $event->form_checker_result_status = 'new';
                    }

                    FormCheckerResultStatuses::ACCEPTED->value;
                    FormCheckerResultStatuses::REJECTED->value;

                    return $event;
                })
                ->filter(function (Event $event) use ($user) {
                    return empty(collect($event->form_structure->fields)->where('checker_user_id', $user->id)->count()) == false;
                });
            $forms = Form::query()
                ->whereIn('id', $events->pluck('form_id'))
                ->get();
            $departaments = Departament::where('id', $events->pluck('departament_id'));
            $formResults = FormResult::where('event_id', $events->pluck('id'))->get();

            return view(self::$views['index'], [
                'formCheckers' => $formCheckers,
                'forms' => $forms,
                'events' => $events,
                'departaments' => $departaments,
                'formCheckerResults' => $formCheckerResults,
                'formResults' => $formResults,
            ]);
        } catch (HumanException $e) {
            return redirect()
                ->route('web.index.index')
                ->withErrors([$e->getMessage()]);
        } catch (Throwable $e) {
            abort(500);
        }
    }

    public function accept(Request $request)
    {
        try {
            $user = Auth::user();

            $this->checkAccess($user);

            throw_if(empty($request->input('field')), new HumanException('Ошибка обработки поля! Код ошибки: #1000'));

            $event = Event::find($request->input('event', null));

            throw_if(empty($event), new HumanException('Ошибка обработки события! Код ошибки: #1001'));

            $findeds = FormCheckerResult::query()
                ->where('form_id', $event->form_id)
                ->where('event_id', $event->id)
                ->where('field_id', $request->input('field'))
                ->count();

            throw_if(empty($findeds) == false, new HumanException('Ошибка обработки проверки! Код ошибки: #1002'));

            (new FormCheckerResult())->fill([
                'user_id' => $user->id,
                'form_id' => $event->form_id,
                'event_id' => $event->id,
                'field_id' => $request->input('field'),
                'status' => FormCheckerResultStatuses::ACCEPTED,
            ])->save();

            return Responser::returnSuccess();
        } catch (HumanException $e) {
            return Responser::returnError([$e->getMessage()]);
        } catch (Throwable $e) {
            return Responser::returnError(['Внутренняя ошибка!']);
        }
    }

    public function reject(Request $request)
    {
        try {
            $user = Auth::user();

            $this->checkAccess($user);

            throw_if(empty($request->input('field')), new HumanException('Ошибка обработки поля! Код ошибки: #1000'));

            $event = Event::find($request->input('event', null));

            throw_if(empty($event), new HumanException('Ошибка обработки события! Код ошибки: #1001'));

            $findeds = FormCheckerResult::query()
                ->where('form_id', $event->form_id)
                ->where('event_id', $event->id)
                ->where('field_id', $request->input('field'))
                ->count();

            throw_if(empty($findeds) == false, new HumanException('Ошибка обработки проверки! Код ошибки: #1002'));

            (new FormCheckerResult())->fill([
                'user_id' => $user->id,
                'form_id' => $event->form_id,
                'event_id' => $event->id,
                'field_id' => $request->input('field'),
                'status' => FormCheckerResultStatuses::REJECTED,
            ])->save();

            return Responser::returnSuccess();
        } catch (HumanException $e) {
            return Responser::returnError([$e->getMessage()]);
        } catch (Throwable $e) {
            return Responser::returnError(['Внутренняя ошибка!']);
        }
    }

    protected function checkAccess(User $user = null): void
    {
        if (empty($user)) $user = Auth::user();
        throw_if(empty($user), new HumanException('Ошибка авторизации! Номер ошибки: #1100'));
        throw_if($user->hasAnyAccess(['platform.checker.base']) == false, new HumanException('Ошибка авторизации! Номер ошибки: #1101'));
    }
}
