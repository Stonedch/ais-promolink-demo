<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\HumanException;
use App\Helpers\Responser;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Attachment\File;
use Throwable;

class UserAvatarController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
            $file = new File($request->file('avatar.attachment'));
            $attachment = $file->load();
            $user = Auth::user();
            $user->attachment_id = $attachment->id;
            $user->save();
            return Responser::returnSuccess();
        } catch (HumanException $e) {
            return Responser::returnError([$e->getMessage()]);
        } catch (Throwable $e) {
            return Responser::returnError();
        }
    }
}
