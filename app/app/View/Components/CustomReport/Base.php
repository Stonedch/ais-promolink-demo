<?php

namespace App\View\Components\CustomReport;

use App\Services\Normalizers\PhoneNormalizer;
use App\Helpers\Responser;
use App\Models\CustomReportType;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class Base extends Component
{
    protected const VIEW = 'components.custom-report.base';

    protected Collection $types;

    public function __construct()
    {
        $this->types = $this->getCustomReportTypes();
    }

    protected function getCustomReportTypes(): Collection
    {
        $user = Auth::user();
        return $user ? CustomReportType::byUser(Auth::user()) : new Collection();
    }

    public function render(): View|Closure|string
    {
        return view(self::VIEW, [
            'types' => $this->types,
        ]);
    }
}
