<?php

use App\Http\Controllers\InternosEmpresa;
use App\Http\Controllers\ResultadosAnualesEmpresa;
use App\Http\Controllers\TransaccionesInternas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ValidateToken;

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

Route::middleware(ValidateToken::class)->get('/transacciones-internas/{codigo}', [TransaccionesInternas::class, 'get']);
Route::middleware(ValidateToken::class)->get('/transacciones-internas', [TransaccionesInternas::class, 'list']);
Route::middleware(ValidateToken::class)->get('/empresas/{cik}/internos', [InternosEmpresa::class, 'get']);
Route::middleware(ValidateToken::class)->get('/empresas/{cik}/resultados-anuales', [ResultadosAnualesEmpresa::class, 'get']);
