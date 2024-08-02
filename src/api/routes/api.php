<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
use App\Http\Controllers\DummyDataController;
use App\Http\Controllers\Api\FarmaciasController;

Route::middleware('auth.basic.custom')->group(function () {
    Route::get('/farmacias', [FarmaciasController::class, 'index']);
    Route::get('/farmacias/{id}', [FarmaciasController::class, 'read']);
    Route::post('/farmacias', [FarmaciasController::class, 'create']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
});
/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/
