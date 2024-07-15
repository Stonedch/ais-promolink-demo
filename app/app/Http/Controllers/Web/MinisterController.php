<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\HumanException;
use App\Helpers\FormHelper;
use App\Http\Controllers\Controller;
use App\Models\Departament;
use App\Models\DepartamentType;
use App\Models\District;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Throwable;

class MinisterController extends Controller
{
    public static array $views = [
        'index' => 'web.minister.index',
        'by-district' => 'web.minister.by-district',
        'by-departament-type' => 'web.minister.by-departament-type',
    ];

    public function index(): View|RedirectResponse
    {
        try {
            $this->checkAccess();

            $response = [
                'districts' => District::orderBy('name')->get(),
                'departaments' => Departament::all(),
                'departamentTypes' => DepartamentType::where('show_minister_view', true)->get(),
            ];

            return view(self::$views['index'], $response);
        } catch (HumanException $e) {
            return redirect()
                ->route('web.index.index')
                ->withErrors([$e->getMessage()]);
        } catch (Throwable $e) {
            dd($e);
            abort(500);
        }
    }

    public function byDistrict(
        District $district,
        Departament $departament
    ): View|RedirectResponse {
        try {
            $this->checkAccess();

            $response = [
                'district' => $district,
                'departament' => $departament,
            ];

            if ($district->exists && $departament->exists) {
                $response = array_merge(
                    FormHelper::byDepartaments(Departament::whereNotNull('departament_type_id')->get())->toArray(),
                    $response
                );

                $departamentTypeEvents = [];

                collect($response['writedEvents'])->map(function ($events, $key) use (&$departamentTypeEvents, $departament) {
                    $finded = collect($events)->where('departament_id', $departament->id);

                    if ($finded->count()) {
                        $departamentTypeEvents[$key] = $finded->keyBy('id')->toArray();
                    }
                });

                $departamentTypeEvents = collect($departamentTypeEvents);

                $departamentTypeEventsNotGroupping = [];

                $departamentTypeEvents->map(function ($events) use (&$departamentTypeEventsNotGroupping, $departament) {
                    $departamentTypeEventsNotGroupping = array_merge(
                        $departamentTypeEventsNotGroupping,
                        collect($events)->where('departament_id', $departament->id)->toArray(),
                    );
                });

                $includeForms = collect($response['forms'])->whereIn(
                    'id',
                    collect($departamentTypeEventsNotGroupping)->pluck('form_id'),
                );

                $response['forms'] = $includeForms;
                $response['writedEvents'] = $departamentTypeEvents;
            } elseif ($district->exists) {
                $response['districts'] = new Collection();
                $response['departaments'] = Departament::where('district_id', $district->id)->orderBy('name')->get();
            } else {
                $response['districts'] = District::orderBy('name')->get();
                $response['departaments'] = Departament::orderBy('name')->get();
            }

            return view(self::$views['by-district'], $response);
        } catch (HumanException $e) {
            return redirect()
                ->route('web.index.index')
                ->withErrors([$e->getMessage()]);
        } catch (Throwable) {
            abort(500);
        }
    }

    public function byDepartamentType(
        DepartamentType $departamentType,
        District $district,
        Departament $departament
    ): View|RedirectResponse {
        try {
            $this->checkAccess();

            $response = [
                'departamentType' => $departamentType,
                'district' => $district,
                'departament' => $departament,
            ];

            if ($departamentType->exists && $district->exists && $departament->exists) {
                $response = array_merge(
                    FormHelper::byDepartaments(Departament::whereNotNull('departament_type_id')->get())->toArray(),
                    $response
                );

                $departamentTypeEvents = [];

                collect($response['writedEvents'])->map(function ($events, $key) use (&$departamentTypeEvents, $departament,) {
                    $finded = collect($events)->where('departament_id', $departament->id);

                    if ($finded->count()) {
                        $departamentTypeEvents[$key] = $finded->keyBy('id')->toArray();
                    }
                });

                $departamentTypeEvents = collect($departamentTypeEvents);

                $departamentTypeEventsNotGroupping = [];

                $departamentTypeEvents->map(function ($events) use (&$departamentTypeEventsNotGroupping, $departament) {
                    $departamentTypeEventsNotGroupping = array_merge(
                        $departamentTypeEventsNotGroupping,
                        collect($events)->where('departament_id', $departament->id)->toArray(),
                    );
                });

                $includeForms = collect($response['forms'])->whereIn(
                    'id',
                    collect($departamentTypeEventsNotGroupping)->pluck('form_id'),
                );

                $response['forms'] = $includeForms;
                $response['writedEvents'] = $departamentTypeEvents;
            } elseif ($departamentType->exists && $district->exists) {
                $response['departaments'] = Departament::query()
                    ->where('departament_type_id', $departamentType->id)
                    ->where('district_id', $district->id)
                    ->orderBy('name')
                    ->get();
            } elseif ($departamentType->exists) {
                $response['departaments'] = Departament::where('departament_type_id', $departamentType->id)->orderBy('name')->get();
                $response['districts'] = District::query()->whereIn('id', $response['departaments']->pluck('district_id'))->orderBy('name')->get();
            } else {
                $response['departamentTypes'] = DepartamentType::orderBy('name')->get();
                $response['departaments'] = Departament::orderBy('name')->get();
            }

            return view(self::$views['by-departament-type'], $response);
        } catch (HumanException $e) {
            return redirect()
                ->route('web.index.index')
                ->withErrors([$e->getMessage()]);
        } catch (Throwable $e) {
            dd($e);
            abort(500);
        }
    }

    protected function checkAccess(User $user = null): void
    {
        if (empty($user)) $user = Auth::user();
        throw_if(empty($user), new HumanException('Ошибка авторизации!'));
        throw_if($user->hasAnyAccess(['platform.min.base']) == false, new HumanException("Ошибка авторизации!"));
    }
}
