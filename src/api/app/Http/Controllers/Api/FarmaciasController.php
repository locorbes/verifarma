<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        $farmacias = Farmacia::all();
        return response()->json($farmacias);
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
    public function read($id)
    {
        $farmacia = Farmacia::find($id);

        if (!$farmacia) {
            return response()->json(['message' => 'Farmacia no encontrada'], 404);
        }

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
    public function index(Request $request)
    {
        if ($request->has(['lat', 'lon'])) {
            $request->validate([
                'lat' => 'required|numeric',
                'lon' => 'required|numeric',
            ]);

            $lat = $request->input('lat');
            $lon = $request->input('lon');

            $farmacias = Farmacia::all();

            $farmaciaCercana = null;
            $minDistancia = PHP_FLOAT_MAX;

            foreach ($farmacias as $farmacia) {
                $distancia = $this->calculate($lat, $lon, $farmacia->latitud, $farmacia->longitud);
                if ($distancia < $minDistancia) {
                    $minDistancia = $distancia;
                    $farmaciaCercana = $farmacia;
                    $farmaciaCercana->distancia = $distancia;
                }
            }

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
}

