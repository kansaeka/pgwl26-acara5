<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// GEOJSON API
Route::get('/points', [ApiController::class, 'geojsonPoints'])
    ->name('geojson.points');

Route::get('/point/{id}', [ApiController::class, 'geojsonPoint'])
    ->name('geojson.point');

// CUKUP SATU BARIS INI: Menggunakan {id?} agar bisa dipanggil dengan ID atau tanpa ID
Route::get('/polyline/{id?}', [ApiController::class, 'geojsonPolyline'])
    ->name('geojson.polylines');

// CUKUP SATU BARIS INI: Menggunakan {id?} agar nama rutenya tidak duplikat/bentrok
Route::get('/polygon/{id?}', [ApiController::class, 'geojsonPolygon'])
    ->name('geojson.polygons');
