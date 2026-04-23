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

    // Kolom yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'name',
        'description',
        'geom' // Nama kolom spasial di database
    ];

    public function geojson_polylines()
    {
        $polylines = $this->select(DB::raw('id, ST_AsGeoJSON(geom) as geojson, name, description, image, created_at, updated_at'))->get();

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
