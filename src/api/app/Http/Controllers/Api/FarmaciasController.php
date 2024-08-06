<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use app\Http\Requests\FindPharmacyRequest;
use App\Models\Farmacia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
/**
 * @OA\Info(
 *     title="CHALLENGE DOCS",
 *     version="1.0.0",
 *     description="Esta es la documentación del Challenge para Verifarma"
 * )
 */
class FarmaciasController extends Controller
{
    public function all()
    {
        // No es una buena idea utilizar la Facade de los modelos directamente en el controller
        // no es necesario una variable adicional si no se va a utilizar
        return response()->json(Farmacia::all());
    }
    /**
     * @OA\Get(
     *     path="/api/farmacias/{id}",
     *     security={{"basicAuth":{}}},
     *     summary="Obtiene una farmacia por ID",
     *     description="Obtiene los datos de una farmacia dependiendo del ID.",
     *     operationId="getFarmaciaID",
     *     tags={"Farmacias"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la farmacia",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Datos obtenidos correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="nombre", type="string", example="Farmacia Central"),
     *             @OA\Property(property="direccion", type="string", example="Dirección 123"),
     *             @OA\Property(property="latitud", type="number", example=-34.5167375),
     *             @OA\Property(property="longitud", type="number", example=-58.4758169)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Farmacia no encontrada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Farmacia no encontrada")
     *         )
     *     )
     * )
     */
    public function read(Farmacia $farmacia)
    {
        // misma recomendacion, evitar usar facade de modelos directamente en el controller
        // se puede ahorrar una linea usando directamente la variable en el if (no es ni mejor ni peor, es una alternativa)
        // evitar tener mas de un return en caso de no ser necesario
        // en esta situacion, podriamos usar un RouteBinding (dentro de RouteServiceProvider) para evitar tener que buscar la farmacia aca
       /* if (!$farmacia = Farmacia::find($id)) {
            return response()->json(['message' => 'Farmacia no encontrada'], 404);
        }*/

        return response()->json($farmacia);
    }
    /**
     *
     * @OA\Get(
     *     path="/api/farmacias",
     *     security={{"basicAuth":{}}},
     *     summary="Obtiene la farmacia más cercana",
     *     description="Obtiene la farmacia más cercana. Si no se proporciona lat (latitud) y lon (longitud) devuelve todas las farmacias.",
     *     operationId="getFarmacia",
     *     tags={"Farmacias"},
     *     @OA\Parameter(
     *         name="lat",
     *         in="query",
     *         description="Latitud de la ubicación",
     *         required=false,
     *         @OA\Schema(type="number", example="-34.5167375")
     *     ),
     *     @OA\Parameter(
     *         name="lon",
     *         in="query",
     *         description="Longitud de la ubicación",
     *         required=false,
     *         @OA\Schema(type="number", example="-58.4758169")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Datos obtenidos correctamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="nombre", type="string", example="Farmacia Central"),
     *                 @OA\Property(property="direccion", type="string", example="Dirección 123"),
     *                 @OA\Property(property="latitud", type="number", example=-34.5167375),
     *                 @OA\Property(property="longitud", type="number", example=-58.4758169)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se encontraron farmacias cercanas",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="No se encontraron farmacias cercanas")
     *         )
     *     )
     * )
     */
    public function index(FindPharmacyRequest $request)
    {
        //demasiado codigo para estar dentro del controller
        // es una buena idea separar el metodo en 2, uno para obtener todas las farmacias y otro para obtener la mas cercana
        // quien debe validar la request es el objeto request
        // la modificacion que planteo seria para la busqueda en este caso
        // cuando llega aca, FindPharmacyRequest ya valido los atributos, por ende ya sabemos que existen y podemos hacer lo siguiente

        if ($request->has(['lat', 'lon'])) {
            $request->validate([
                'lat' => 'required|numeric',
                'lon' => 'required|numeric',
            ]);


            $farmaciaCercana = $this->getFarmaciaCercana($request);

            if ($farmaciaCercana) {
                return response()->json($farmaciaCercana);
            }

            return response()->json(['message' => 'No se encontraron farmacias cercanas'], 404);
        } else {
            return Farmacia::all();
        }
    }
    /**
     * @OA\Post(
     *     path="/api/farmacias",
     *     security={{"basicAuth":{}}},
     *     summary="Crea una nueva farmacia",
     *     description="Crea una nueva farmacia en la base de datos.",
     *     operationId="createFarmacia",
     *     tags={"Farmacias"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombre", "direccion", "latitud", "longitud"},
     *             @OA\Property(
     *                 property="nombre",
     *                 type="string",
     *                 example="Farmacia Central"
     *             ),
     *             @OA\Property(
     *                 property="direccion",
     *                 type="string",
     *                 example="Calle 123"
     *             ),
     *             @OA\Property(
     *                 property="latitud",
     *                 type="number",
     *                 format="float",
     *                 example=-58.4758169
     *             ),
     *             @OA\Property(
     *                 property="longitud",
     *                 type="number",
     *                 format="float",
     *                 example=-118.243683
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Farmacia creada correctamente",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Solicitud inválida",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Los datos proporcionados no son válidos"
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="string",
     *                 example="Descripción de errores"
     *             )
     *         )
     *     )
     * )
     *
     * Misma recomendacion relacionada con la request. la validacion es parte de la request, no del controller.
     * sin la validacion este metodo seria de dos lineas
     * usar servicio para interactuar con los modelos
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $farmacia = Farmacia::create($validator->validated());

        return response()->json($farmacia, 201);
    }

    /**
     * Esta funcion, como la nueva propuesta por mi deberian formar parte de un service que se inyecte en el controller
     * los attributos deberian estar tipados para evitar conflictos de tipos
     */
    private function calculate($lat1, $lon1, $lat2, $lon2)
    {
        $radioTierra = 6371; //El radio de la tierra en kilometros u_u
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distancia = $radioTierra * $c;
        return $distancia;
    }

    /**
     * @param  FindPharmacyRequest  $request
     * @return mixed|null
     * Esto lo extraigo para separar responsabilidades, pero deberia ir en una clase service, no en el controller
     * misma recomendacion acerca de la utilizacion de facade de modelos en el controller
     */
    public function getFarmaciaCercana(FindPharmacyRequest $request): mixed
    {
        $farmaciaCercana = null;
        $minDistancia = PHP_FLOAT_MAX;

        foreach (Farmacia::all() as $farmacia) {
            $distancia = $this->calculate($request->float('lat'), $request->float('lon'), $farmacia->latitud,
                $farmacia->longitud);
            if ($distancia < $minDistancia) {
                $minDistancia = $distancia;
                $farmaciaCercana = $farmacia;
                $farmaciaCercana->distancia = $distancia;
            }
        }
        return $farmaciaCercana;
    }
}

