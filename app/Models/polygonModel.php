<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class polygonModel extends Model
{
    protected $table = 'polygons';
    protected $guarded = ['id']; // Sudah benar & aman untuk proses update

    // PERBAIKAN: Menambahkan opsi parameter $id agar bisa mengambil semua atau satu data saja
    public function geojson_polygons($id = null)
    {
        // 1. Mulai query dasar select spasial PostGIS
        $query = $this->select(DB::raw('id, ST_AsGeoJSON(geom) as geojson, name, description, image, created_at, updated_at'));

        // 2. Jika ID dikirim (berarti dipanggil oleh halaman edit), filter berdasarkan ID tersebut
        if ($id !== null) {
            $query->where('id', $id);
        }

        // 3. Eksekusi ambil data dari database
        $polygons = $query->get();

        $geojson = [
            'type' => 'FeatureCollection',
            'features' => [],
        ];

        foreach ($polygons as $p) {
            $feature = [
                'type' => 'Feature',
                'geometry' => json_decode($p->geojson),
                'properties' => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'description' => $p->description,
                    'image' => $p->image,
                    'created_at' => $p->created_at,
                    'updated_at' => $p->updated_at,
                ],
            ];

            $geojson['features'][] = $feature;
        }

        return $geojson;
    }
}
