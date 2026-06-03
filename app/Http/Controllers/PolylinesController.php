<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\polylinesModel; // Pastikan huruf kapitalnya sesuai nama file model Anda
use Illuminate\Support\Facades\DB; // Ditambahkan untuk menggunakan fungsi DB::raw

class PolylinesController extends Controller
{
    protected $polyline;

    public function __construct()
    {
        $this->polyline = new polylinesModel();
    }

    public function index()
    {
        // Ambil semua data polyline untuk dikirim ke view jika perlu
        $polylines = $this->polyline->all();
        return view('peta', compact('polylines'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        // Validasi Input bawaan modal tambah Anda
        $request->validate(
            [
                'geometry_polyline' => 'required',
                'name' => 'required|string|max:255',
                'description' => 'required',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5048',
            ],
            [
                'geometry_polyline.required' => 'Field geometry polyline harus diisi.',
                'name.required' => 'Field nama harus diisi.',
                'description.required' => 'Field deskripsi harus diisi.',
            ]
        );

        // Create Directory for images if it doesn't exist
        if (!is_dir('storage/images')) {
            mkdir('./storage/images', 0777);
        }

        // Get the upload images
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name_image = time() . "_polyline." . strtolower($image->getClientOriginalExtension());
            $image->move('storage/images', $name_image);
        } else {
            $name_image = null;
        }

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'geom' => DB::raw("ST_GeomFromText('" . $request->geometry_polyline . "', 4326)"),
            'image' => $name_image,
        ];

        try {
            $this->polyline->create($data);
            return redirect()->route('peta')->with('success', 'Berhasil Menyimpan Data Polyline.');
        } catch (\Exception $e) {
            return redirect()->route('peta')->with('error', 'Gagal Menyimpan Data Polyline: ' . $e->getMessage());
        }
    }

    public function show(string $id)
    {
        //
    }

    // Ambil data untuk ditampilkan ke halaman map-edit-polyline
    public function edit(string $id)
    {
        $data = [
            'title' => 'Edit Polyline',
            'id' => $id,
        ];
        return view('map-edit-polyline', $data);
    }

    // Eksekusi perubahan data garis (Polyline)
    public function update(Request $request, string $id)
    {
        $request->validate(
            [
                'geometry' => 'required', // Menangkap 'geometry' dari form blade edit
                'name' => 'required|string|max:255',
                'description' => 'required',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5048',
            ],
            [
                'geometry.required' => 'Geometri garis (polyline) tidak boleh kosong.',
                'name.required' => 'Nama garis harus diisi.',
                'description.required' => 'Deskripsi harus diisi.',
            ]
        );

        // 1. Cari data polyline berdasarkan ID
        $polyline = $this->polyline->find($id);

        if (!$polyline) {
            return redirect()->route('peta')->with('error', 'Data polyline tidak ditemukan!');
        }

        $name_image = $polyline->image;

        // 2. Handling Gambar Baru (Hapus gambar lama kalau user upload gambar baru)
        if ($request->hasFile('image')) {
            if ($name_image != null && file_exists('./storage/images/' . $name_image)) {
                unlink('./storage/images/' . $name_image);
            }

            $image = $request->file('image');
            $name_image = time() . "_polyline." . strtolower($image->getClientOriginalExtension());
            $image->move('storage/images', $name_image);
        }

        // 3. Susun data update menggunakan format ST_GeomFromText PostGIS
        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'geom' => DB::raw("ST_GeomFromText('" . $request->geometry . "', 4326)"),
            'image' => $name_image,
        ];

        // 4. Eksekusi update dengan try-catch
        try {
            $polyline->update($data);
            return redirect()->route('peta')->with('success', 'Data polyline berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->route('peta')->with('error', 'Gagal memperbarui data polyline: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        // Mencari data polyline berdasarkan ID
        $polyline = $this->polyline->find($id);

        if (!$polyline) {
            return redirect()->route('peta')->with('error', 'Data polyline tidak ditemukan!');
        }

        $image = $polyline->image;

        // Menghapus data dari database
        if (!$polyline->delete()) {
            return redirect()->route('peta')->with('error', 'Gagal menghapus data polyline.');
        }

        // Menghapus file gambar jika ada
        if ($image != null && file_exists('./storage/images/' . $image)) {
            unlink('./storage/images/' . $image);
        }

        return redirect()->route('peta')->with('success', 'Data polyline berhasil dihapus.');
    }
}
