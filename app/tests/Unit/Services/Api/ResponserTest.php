<?php

namespace Tests\Unit\Services\Api;

use App\Services\Api\Responser;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class ResponserTest extends TestCase
{
    /** @test */
    public function it_returns_basic_json_response()
    {
        $response = Responser::returnJson();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['state' => false], $response->getData(true));
    }

    /** @test */
    public function it_returns_success_response()
    {
        $data = ['id' => 1, 'name' => 'Test'];
        $response = Responser::returnSuccess($data);

        $expected = [
            'state' => true,
            'data' => $data
        ];

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expected, $response->getData(true));
    }

    /** @test */
    public function it_returns_success_with_custom_status()
    {
        $response = Responser::returnSuccess([], 201);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals(['state' => true], $response->getData(true));
    }

    /** @test */
    public function it_returns_error_response()
    {
        $errors = ['Error message', 'Another error'];
        $response = Responser::returnError($errors);

        $expected = [
            'state' => false,
            'error' => 'Error message, Another error'
        ];

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expected, $response->getData(true));
    }

    /** @test */
    public function it_returns_error_with_custom_status()
    {
        $response = Responser::returnError(['Not found'], 404);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals([
            'state' => false,
            'error' => 'Not found'
        ], $response->getData(true));
    }

    /** @test */
    public function it_handles_null_data_and_errors()
    {
        $response1 = Responser::returnSuccess();
        $response2 = Responser::returnError();

        $this->assertEquals(['state' => true], $response1->getData(true));
        $this->assertEquals(['state' => false], $response2->getData(true));
    }

    /** @test */
    public function it_returns_complex_response()
    {
        $data = ['user' => ['id' => 1]];
        $errors = ['Validation failed'];
        $response = Responser::returnJson($data, $errors, true, 422);

        $expected = [
            'state' => true,
            'data' => $data,
            'error' => 'Validation failed'
        ];

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals($expected, $response->getData(true));
    }
}