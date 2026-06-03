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
        // 1. Validasi Input (Pastikan menggunakan 'geometry_polygons' sesuai modal tambah)
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
        try {
            $this->polygon->create($data);
            return redirect()->route('peta')->with('success', 'Data polygon berhasil disimpan!');
        } catch (\Exception $e) {
            return redirect()->route('peta')->with('error', 'Gagal menyimpan ke database: ' . $e->getMessage());
        }
    }

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
        $data = [
            'title' => 'Edit Polygon',
            'id' => $id,
        ];
        return view('map-edit-polygon', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validasi input data update (Menangkap 'geometry' dari form blade edit)
        $request->validate(
            [
                'geometry' => 'required',
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:10048',
            ],
            [
                'geometry.required' => 'Field geometry area (polygon) harus diisi.',
                'name.required' => 'Field name harus diisi.',
                'description.required' => 'Field description harus diisi.',
            ]
        );

        // Cari data polygon berdasarkan ID
        $polygon = $this->polygon->find($id);

        if (!$polygon) {
            return redirect()->route('peta')->with('error', 'Data polygon tidak ditemukan!');
        }

        $name_image = $polygon->image;

        // Ambil file gambar baru jika diupload
        if ($request->hasFile('image')) {
            // Hapus gambar lama dari server jika ada
            if ($name_image != null && file_exists('./storage/images/' . $name_image)) {
                unlink('./storage/images/' . $name_image);
            }

            $image = $request->file('image');
            $name_image = time() . "_polygon." . strtolower($image->getClientOriginalExtension());
            $image->move('storage/images', $name_image);
        }

        // Format ST_GeomFromText untuk update spasial PostGIS agar sama dengan store
        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'geom' => DB::raw("ST_GeomFromText('" . $request->geometry . "', 4326)"),
            'image' => $name_image,
        ];

        try {
            $polygon->update($data);
            return redirect()->route('peta')->with('success', 'Data polygon berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->route('peta')->with('error', 'Gagal memperbarui database: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Cari data polygon berdasarkan ID
        $polygon = $this->polygon->find($id);

        // Jika data tidak ditemukan
        if (!$polygon) {
            return redirect()->route('peta')->with('error', 'Data polygon tidak ditemukan!');
        }

        // Simpan nama gambar sebelum data dihapus
        $image = $polygon->image;

        // Hapus data dari database
        if (!$polygon->delete()) {
            return redirect()->route('peta')->with('error', 'Gagal menghapus data polygon!');
        }

        // Hapus file gambar jika ada
        if ($image != null && file_exists('./storage/images/' . $image)) {
            unlink('./storage/images/' . $image);
        }

        return redirect()->route('peta')->with('success', 'Data polygon berhasil dihapus!');
    }
}
