<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
