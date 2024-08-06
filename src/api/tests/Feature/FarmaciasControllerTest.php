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

        // falta asertar que la farmacia fue creada correctamente. con los datos enviados
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

        // deberiamos asertar si el error producido es el esperado. en este caso, falta el campo nombre
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

        // este test deberia testear que la response es la que esperamos.
        // esperamos una farmacia, esperamos todas? esa deberia de ser la asercion
        // separando el endpoint que busca la mas cercana y las que trae todas se simplifica el test
        $this->assertEquals(200, $response->status());
    }
}
