<?php

namespace App\Helpers;

class Responser
{
    public static function returnJson(array $data = [], array $errors = [], bool $state = false)
    {
        $response = [
            'state' => $state,
        ];

        if (empty($data) == false) {
            $response['data'] = $data;
        }

        if (empty($errors) == false) {
            $response['error'] = implode(', ', $errors);
        }

        return response()->json($response);
    }

    public static function returnSuccess(array $data = [])
    {
        $state = true;
        $errors = [];

        return Responser::returnJson($data, $errors, $state);
    }

    public static function returnError(array $errors = [])
    {
        $state = false;
        $data = [];

        return Responser::returnJson($data, $errors, $state);
    }
}
