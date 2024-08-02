<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\FarmaciasController;
use Illuminate\Http\JsonResponse;

class FarmaciasControllerTest extends TestCase
{
    public function test_success_create_farmacia()
    {
        $controller = new FarmaciasController();

        $request = new Request();
        $request->merge([
            'nombre' => 'Farmacia Test',
            'direccion' => 'Direccion Test',
            'latitud' => -34.5167375,
            'longitud' => -58.4758169,
        ]);

        $response = $controller->create($request);

        $this->assertInstanceOf(JsonResponse::class, $response);

        $this->assertEquals(201, $response->status());
    }
    public function test_fail_create_farmacia()
    {
        $controller = new FarmaciasController();

        $request = new Request();
        $request->merge([
            'direccion' => 'Direccion Test',
            'latitud' => -34.5167375,
            'longitud' => -58.4758169,
        ]);

        $response = $controller->create($request);

        $this->assertInstanceOf(JsonResponse::class, $response);

        $this->assertEquals(422, $response->status());
    }
    public function test_success_find_nearest()
    {
        $controller = new FarmaciasController();

        $request = new Request();
        $request->merge([
            'lat' => -34.5167375,
            'lon' => -58.4758169,
        ]);

        $response = $controller->index($request);

        $this->assertEquals(200, $response->status());
    }
}
