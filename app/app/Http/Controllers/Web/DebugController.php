<?php

namespace App\Http\Controllers\Web;

use App\Helpers\FormHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

// Please clear me after debug
class DebugController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $response = FormHelper::byUser($user);
        return view('web.debug.index', $response);
    }
}
