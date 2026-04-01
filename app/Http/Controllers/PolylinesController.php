<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\polylinesModel;
use Illuminate\Support\Facades\DB;

class PolylinesController extends Controller
{
    public $polylines;

    public function __construct()
    {
        $this->polylines = new polylinesModel();
    }

    public function index()
    {
        $polylines = $this->polylines->all();
        return view('peta', compact('polylines'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input (Pastikan key pesan error sama dengan key input)
        $request->validate(
            [
                'geometry_polylines' => 'required',
                'name' => 'required|string|max:255',
                'description' => 'required|string',
            ],
            [
                // Perhatikan 'geometry_polylines' di sini harus sama dengan di atas
                'geometry_polylines.required' => 'Field geometry polyline harus diisi.',
                'name.required' => 'Field name harus diisi.',
                'description.required' => 'Field description harus diisi.',
            ]
        );

        // 2. Persiapan Data Spasial
        // Kita simpan dulu value-nya ke variabel agar lebih bersih
        $geometry = $request->input('geometry_polylines');

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            // Gunakan ST_GeomFromText dengan SRID 4326
            'geom' => DB::raw("ST_GeomFromText('" . $geometry . "', 4326)"),
        ];

        // 3. Simpan data ke database
        try {
            $this->polylines->create($data);
            return redirect()->route('peta')->with('success', 'Data polyline berhasil disimpan!');
        } catch (\Exception $e) {
            // Jika koordinat salah format (bukan LINESTRING), error akan tertangkap di sini
            return redirect()->route('peta')->with('error', 'Gagal menyimpan: Format koordinat tidak valid atau terjadi masalah database.');
        }
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
