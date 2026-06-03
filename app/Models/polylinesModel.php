<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class polylinesModel extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'polylines';

    // Kolom yang boleh diisi (Mass Assignment) - Sudah Aman & Lengkap
    protected $fillable = [
        'name',
        'description',
        'geom', // Nama kolom spasial di database
        'image'
    ];

    // PERBAIKAN: Menambahkan opsi parameter $id untuk mendukung filter maps edit garis tunggal
    public function geojson_polylines($id = null)
    {
        // 1. Inisialisasi query select dasar PostGIS
        $query = $this->select(DB::raw('id, ST_AsGeoJSON(geom) as geojson, name, description, image, created_at, updated_at'));

        // 2. Jika ID dilemparkan dari Controller, filter query khusus untuk ID tersebut saja
        if ($id !== null) {
            $query->where('id', $id);
        }

        // 3. Eksekusi query untuk mendapatkan data
        $polylines = $query->get();

        $geojson = [
            'type' => 'FeatureCollection',
            'features' => [],
        ];

        foreach ($polylines as $p) {
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
