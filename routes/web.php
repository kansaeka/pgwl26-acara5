<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PointsController;
use App\Http\Controllers\PolylinesController;
use App\Http\Controllers\PolygonsController;
use App\Http\Controllers\ApiController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/', [PageController::class, 'landingpage'])->name('home');
Route::get('/peta', [PageController::class, 'peta'])
    ->middleware(['auth', 'verified'])
    ->name('peta');
Route::get('/tabel', [PageController::class, 'tabel'])->name('tabel');

// ================= RUTE UNTUK POINTS =================
Route::post('/points', [PointsController::class, 'store'])->name('points.store');
Route::delete('/delete-points/{id}', [PointsController::class, 'destroy'])->name('points.delete');
Route::get('/edit-point/{id}', [PointsController::class, 'edit'])->name('point.edit');
Route::patch('/update-point/{id}', [PointsController::class, 'update'])->name('point.update');

// ================= RUTE UNTUK POLYLINES =================
Route::post('/polyline', [PolylinesController::class, 'store'])->name('polyline.store');
Route::delete('/delete-polyline/{id}', [PolylinesController::class, 'destroy'])->name('polyline.delete');
Route::get('/edit-polyline/{id}', [PolylinesController::class, 'edit'])->name('polyline.edit');     // <-- Ditambahkan
Route::patch('/update-polyline/{id}', [PolylinesController::class, 'update'])->name('polyline.update'); // <-- Ditambahkan

// ================= RUTE UNTUK POLYGONS =================
Route::post('/polygon', [PolygonsController::class, 'store'])->name('polygon.store');
Route::delete('/delete-polygon/{id}', [PolygonsController::class, 'destroy'])->name('polygon.delete');
Route::get('/edit-polygon/{id}', [PolygonsController::class, 'edit'])->name('polygon.edit');     // <-- Ditambahkan
Route::patch('/update-polygon/{id}', [PolygonsController::class, 'update'])->name('polygon.update'); // <-- Ditambahkan

// ================= RUTE API GEOJSON (PERBAIKAN SINKRONISASI) =================
Route::get('/api/points', [ApiController::class, 'geojsonPoints'])->name('geojson.points');
Route::get('/api/points/{id}', [ApiController::class, 'geojsonPoint'])->name('geojson.point'); // <-- Memastikan rute edit point tunggal aman

// PERBAIKAN: Menghapus double semicolon dan menambahkan tanda tanya (?) pada {id?} di rute polygon
Route::get('/api/polylines/{id?}', [ApiController::class, 'geojsonPolyline'])->name('geojson.polylines');
Route::get('/api/polygon/{id?}', [ApiController::class, 'geojsonPolygon'])->name('geojson.polygons');

// ================= AUTH & DASHBOARD =================
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/settings.php';
