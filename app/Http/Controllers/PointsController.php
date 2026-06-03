<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PointsModel;
use Illuminate\Support\Facades\DB; // Ditambahkan untuk menggunakan fungsi DB::raw

class PointsController extends Controller
{
    protected $points;

    public function __construct()
    {
        $this->points = new PointsModel();
    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'geometry_point' => 'required',
                'name' => 'required|string|max:255',
                'description' => 'required',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5048',
            ],
            [
                'geometry_point.required' => 'Field geometry point harus diisi.',
                'name.required' => 'Field nama harus diisi.',
                'name.string' => 'Field nama harus berupa string.',
                'name.max' => 'Field nama tidak boleh lebih dari 255 karakter.',
                'description.required' => 'Field deskripsi harus diisi.',
                'description.string' => 'Field deskripsi harus berupa string.',
                'image.image' => 'File harus berupa file gambar',
                'image.mimes' => 'File gambar harus berformat jpeg, png, atau jpg',
                'image.max' => 'File gambar tidak boleh lebih dari 5MB',
            ]
        );

        //Create Directory for images if it doesn't exist
        if (!is_dir('storage/images')) {
            mkdir('./storage/images', 0777);
        }

        //Get the upload images
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name_image = time() . "_point." . strtolower($image->getClientOriginalExtension());
            $image->move('storage/images', $name_image);
        } else {
            $name_image = null;
        }

        $data = [
            'geom' => $request->geometry_point,
            'name' => $request->name,
            'description' => $request->description,
            'image' => $name_image,
        ];

        // Simpan data ke database
        if (!$this->points->create($data)) {
            return redirect()->route('peta')
                ->with('error', 'Gagal Menyimpan Data Point.');
        }

        return redirect()->route('peta')
            ->with('success', 'Berhasil Menyimpan Data Point.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $data=[
            'title' => 'Edit Point',
            'id' => $id,
        ];
        return view('map-edit-point', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate(
            [
                'geometry' => 'required', // Menyesuaikan name="geometry" pada form modal edit
                'name' => 'required|string|max:255',
                'description' => 'required',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5048',
            ],
            [
                'geometry.required' => 'Field geometry harus diisi.',
                'name.required' => 'Field nama harus diisi.',
                'description.required' => 'Field deskripsi harus diisi.',
            ]
        );

        // Cari data berdasarkan ID-nya
        $point = $this->points->findOrFail($id);

        // Ambil nama file gambar lama untuk cadangan
        $name_image = $point->image;

        // Jika user mengunggah foto baru
        if ($request->hasFile('image')) {
            // Hapus gambar lama dari server jika ada
            if ($name_image != null && file_exists('./storage/images/' . $name_image)) {
                unlink('./storage/images/' . $name_image);
            }

            // Simpan gambar yang baru
            $image = $request->file('image');
            $name_image = time() . "_point." . strtolower($image->getClientOriginalExtension());
            $image->move('storage/images', $name_image);
        }

        // PENERAPAN BARU: Gunakan DB::raw ST_GeomFromText PostGIS agar data spasial konsisten
        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'geom' => DB::raw("ST_GeomFromText('" . $request->geometry . "', 4326)"),
            'image' => $name_image,
        ];

        // Jalankan perintah update data ke database dengan proteksi try-catch
        try {
            $point->update($data);
            return redirect()->route('peta')
                ->with('success', 'Data point berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->route('peta')
                ->with('error', 'Gagal memperbarui data point: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        //mencari nama file gambar
        $image = $this->points->find($id)->image;

        //menghapus file gambar jika ada
        if ($image != null) {
            if (file_exists('./storage/images/' . $image)) {
                unlink('./storage/images/' . $image);
            }
        }

        //menghapus data dari database
        if (!$this->points->destroy($id)) {
            return redirect()->route('peta')
                ->with('error', 'Gagal menghapus data point.');
        }

        //kembali ke halaman peta
        return redirect()->route('peta')
            ->with('success', 'Data point berhasil dihapus.');
    }
}
