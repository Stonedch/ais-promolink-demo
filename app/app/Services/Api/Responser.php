<?php

namespace App\Helpers;
namespace App\Services\Api;

use Illuminate\Http\JsonResponse;

class Responser
{
    public static function returnJson(
        ?array $data = null,
        ?array $errors = null,
        bool $state = false,
        int $status = 200
    ): JsonResponse {
        $response = ['state' => $state];

        if (empty($data) == false) $response['data'] = $data;
        if (empty($errors) == false) $response['error'] = implode(', ', $errors);

        return response()->json($response, $status);
    }

    public static function returnSuccess(?array $data = null, int $status = 200): JsonResponse
    {
        return Responser::returnJson(
            state: true,
            data: $data,
            status: $status
        );
    }

    public static function returnError(?array $errors = null, int $status = 200): JsonResponse
    {
        return Responser::returnJson(
            state: false,
            errors: $errors,
            status: $status
        );
    }
}
