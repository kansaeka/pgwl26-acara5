<?php

namespace App\Http\Controllers;

use App\Models\pointsModel;
use App\Models\polygonModel;
use App\Models\polylinesModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB; // Ditambahkan untuk menjalankan query langsung

class ApiController extends Controller
{
    // Deklarasi properti aman dari error PHP terbaru
    protected $points;
    protected $polyline;
    protected $polygon;

    public function __construct()
    {
        $this->points = new pointsModel();
        $this->polyline = new polylinesModel();
        $this->polygon = new polygonModel();
    }

    public function geojsonPoints(): JsonResponse
    {
        $points = $this->points->geojson_points();

        return response()->json(
            $points,
            200,
            [],
            JSON_NUMERIC_CHECK
        );
    }

    public function geojsonPoint($id): JsonResponse
    {
        $point = $this->points->geojson_point($id);

        return response()->json(
            $point,
            200,
            [],
            JSON_NUMERIC_CHECK
        );
    }

    // PERBAIKAN UTAMA: Menangkap $id dan memfilter query PostGIS agar tidak bocor
    public function geojsonPolyline($id = null): JsonResponse
    {
        // 1. Jika ada ID, kita bypass model dan ambil langsung data spesifik menggunakan query builder
        if ($id) {
            $query = DB::select("
                SELECT id, name, description, image, created_at, updated_at,
                ST_AsGeoJSON(geom) AS geojson
                FROM polylines
                WHERE id = ?
            ", [$id]);

            // Format menjadi struktur GeoJSON FeatureCollection tunggal
            $features = [];
            foreach ($query as $q) {
                $features[] = [
                    'type' => 'Feature',
                    'geometry' => json_decode($q->geojson),
                    'properties' => [
                        'id' => $q->id,
                        'name' => $q->name,
                        'description' => $q->description,
                        'image' => $q->image,
                        'created_at' => $q->created_at,
                        'updated_at' => $q->updated_at,
                    ]
                ];
            }

            $polyline = [
                'type' => 'FeatureCollection',
                'features' => $features
            ];
        } else {
            // 2. Jika TIDAK ADA ID, jalankan fungsi bawaan model Anda seperti semula (menampilkan semua di peta utama)
            $polyline = $this->polyline->geojson_polylines($id);
        }

        return response()->json(
            $polyline,
            200,
            [],
            JSON_NUMERIC_CHECK
        );
    }

    // PERBAIKAN UTAMA: Menangkap $id dan memfilter query PostGIS untuk Polygon
    public function geojsonPolygon($id = null): JsonResponse
    {
        // 1. Jika ada ID, ambil data spesifik area tersebut
        if ($id) {
            $query = DB::select("
                SELECT id, name, description, image, created_at, updated_at,
                ST_AsGeoJSON(geom) AS geojson
                FROM polygons
                WHERE id = ?
            ", [$id]);

            $features = [];
            foreach ($query as $q) {
                $features[] = [
                    'type' => 'Feature',
                    'geometry' => json_decode($q->geojson),
                    'properties' => [
                        'id' => $q->id,
                        'name' => $q->name,
                        'description' => $q->description,
                        'image' => $q->image,
                        'created_at' => $q->created_at,
                        'updated_at' => $q->updated_at,
                    ]
                ];
            }

            $polygon = [
                'type' => 'FeatureCollection',
                'features' => $features
            ];
        } else {
            // 2. Jika tidak ada ID, tampilkan semua area di peta utama
            $polygon = $this->polygon->geojson_polygons($id);
        }

        return response()->json(
            $polygon,
            200,
            [],
            JSON_NUMERIC_CHECK
        );
    }
}
