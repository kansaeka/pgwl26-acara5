<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\polygonModel;
use Illuminate\Support\Facades\DB;

class PolygonsController extends Controller
{
    // Fungsi untuk mengkoneksikan model ke controller
    public $polygon;

    public function __construct()
    {
        $this->polygon = new polygonModel();
    }

    public function index()
    {
        // Ambil semua data polygon untuk dikirim ke view jika perlu
        $polygons = $this->polygon->all();
        return view('peta', compact('polygons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input (Pastikan menggunakan 'geometry_polygons' sesuai modal)
        $request->validate(
            [
                'geometry_polygons' => 'required',
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:10048',
            ],
            [
                'geometry_polygons.required' => 'Field geometry polygons harus diisi.',
                'name.required' => 'Field name harus diisi.',
                'description.required' => 'Field description harus diisi.',
                'description.string' => 'Field description harus berupa string.',

            ]
        );

        //Create directory if not exist
        if (!is_dir('storage/images')) {
            mkdir('./storage/images', 0777);
        }

        //Get file image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name_image = time() . "_polygon." . strtolower($image->getClientOriginalExtension());
            $image->move('storage/images', $name_image);
        } else {
            $name_image = null;
        }

        // 2. Persiapan Data (Gunakan DB::raw agar masuk ke kolom spasial PostGIS)
        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'geom' => DB::raw("ST_GeomFromText('" . $request->geometry_polygons . "', 4326)"),
            'image' => $name_image,
        ];

        // 3. Simpan data ke database
        // Menggunakan try-catch agar jika format koordinat salah, aplikasi tidak crash (Error 500)
        try {
            $this->polygon->create($data);
            return redirect()->route('peta')->with('success', 'Data polygon berhasil disimpan!');
        } catch (\Exception $e) {
            return redirect()->route('peta')->with('error', 'Gagal menyimpan ke database: ' . $e->getMessage());
        }
    } // <--- KURUNG PENUTUP FUNGSI STORE SEKARANG SUDAH BENAR

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
