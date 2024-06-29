<?php

use App\Http\Controllers\JenisController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('/jenis', [JenisController::class, 'index']);
Route::post('/jenis/add',[JenisController::class, 'store']);
Route::put('/jenis/update/{id}',[JenisController::class, 'update']);
Route::delete('/jenis/delete/{id}',[JenisController::class, 'delete']);

