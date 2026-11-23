<?php

use App\Http\Controllers\Api\BalitaApiController;
use App\Http\Controllers\Api\KaderApiController;
use App\Http\Controllers\Api\ParentApiController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json([
    'name' => 'Posyandu Gegersuni API',
    'version' => '1.0',
    'endpoints' => [
        'balita' => '/api/balita',
        'orang_tua' => '/api/orang-tua',
        'kader' => '/api/kader',
    ],
]));

Route::get('balita', [BalitaApiController::class, 'index']);
Route::post('balita', [BalitaApiController::class, 'store']);
Route::get('balita/{balita}', [BalitaApiController::class, 'show']);
Route::put('balita/{balita}', [BalitaApiController::class, 'update']);
Route::patch('balita/{balita}', [BalitaApiController::class, 'update']);
Route::delete('balita/{balita}', [BalitaApiController::class, 'destroy']);

Route::get('orang-tua', [ParentApiController::class, 'index']);
Route::post('orang-tua', [ParentApiController::class, 'store']);
Route::get('orang-tua/{parent}', [ParentApiController::class, 'show']);
Route::put('orang-tua/{parent}', [ParentApiController::class, 'update']);
Route::patch('orang-tua/{parent}', [ParentApiController::class, 'update']);
Route::delete('orang-tua/{parent}', [ParentApiController::class, 'destroy']);

Route::get('kader', [KaderApiController::class, 'index']);
Route::post('kader', [KaderApiController::class, 'store']);
Route::get('kader/{kader}', [KaderApiController::class, 'show']);
Route::put('kader/{kader}', [KaderApiController::class, 'update']);
Route::patch('kader/{kader}', [KaderApiController::class, 'update']);
Route::delete('kader/{kader}', [KaderApiController::class, 'destroy']);
