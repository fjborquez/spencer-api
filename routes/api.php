<?php

use App\Http\Controllers\InternosEmpresa;
use App\Http\Controllers\TransaccionesInternas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/transacciones-internas/{codigo}', [TransaccionesInternas::class, 'get']);
Route::get('/transacciones-internas', [TransaccionesInternas::class, 'list']);
Route::get('/empresas/{cik}/internos', [InternosEmpresa::class, 'get']);
