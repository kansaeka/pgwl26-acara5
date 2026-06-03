@extends('layouts.template')

@section('styles')
    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    {{-- Leaflet Draw CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css">

    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        #map {
            height: 100vh;
            width: 100vw;
        }
    </style>
@endsection

@section('content')
    <div id="map"></div>

    {{-- Modal form edit --}}
    <div class="modal" tabindex="-1" id="modalEdit">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Polyline (Garis)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditGeometry" action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Fill name">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="geometry" class="form-label">Geometry (LINESTRING)</label>
                            <textarea class="form-control" id="geometry" name="geometry" rows="3" readonly></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input class="form-control" type="file" id="image" name="image"
                                onchange="document.getElementById('preview-image').src = window.URL.createObjectURL(this.files[0])">
                        </div>
                        <div class="mb-3">
                            <img src="" alt="Preview" id="preview-image" class="img-thumbnail" width="400">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('components.map-navbar')

    {{-- Bootstrap JS Bundle --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Leaflet JS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    {{-- Leaflet Draw JS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>

    {{-- Terraformer JS --}}
    <script src="https://unpkg.com/@terraformer/wkt"></script>

    {{-- jQuery JS --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        // Set view default (Nanti akan otomatis tergeser oleh fitBounds)
        var map = L.map('map').setView([-6.2088, 106.8456], 13);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        /* Digitize Function */
        var drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        var drawControl = new L.Control.Draw({
            draw: false,
            edit: {
                featureGroup: drawnItems,
                edit: true,
                remove: false
            }
        });
        map.addControl(drawControl);

        // Aksi ketika user SELESAI mengedit/menggeser simpul garis di peta
        map.on('draw:edited', function(e) {
            var layers = e.layers;

            layers.eachLayer(function(layer) {
                var drawnJSONObject = layer.toGeoJSON();
                // Konversi koordinat baru hasil editan ke format WKT teks
                var objectGeometry = Terraformer.geojsonToWKT(drawnJSONObject.geometry);

                // Update isi teks di textarea geometry agar siap dikirim ke Controller
                $('#geometry').val(objectGeometry);

                // Tampilkan modal edit untuk konfirmasi akhir data teks
                $('#modalEdit').modal('show');
            });
        });

        // Ambil data GeoJSON untuk 1 rute polyline spesifik berdasarkan ID rute URL
        $.getJSON("{{ route('geojson.polylines', $id) }}", function(data) {
            // Inisialisasi layer tunggal dengan style pilihan
            var polylines = L.geoJSON(data, {
                style: function(feature) {
                    return {
                        color: "#ff7800",
                        weight: 5,
                        opacity: 0.8
                    };
                },
                onEachFeature: function(feature, layer) {
                    // Masukkan ke grup Leaflet Draw agar tombol "Edit" aktif menyala
                    drawnItems.addLayer(layer);

                    // PENTING: Melekatkan database properties langsung ke objek layer Leaflet
                    layer.properties = feature.properties;

                    // Pengisian Form Modal Utama Otomatis saat halaman pertama kali dibuka
                    $('#name').val(feature.properties.name);
                    $('#description').val(feature.properties.description);

                    var wkt = Terraformer.geojsonToWKT(feature.geometry);
                    $('#geometry').val(wkt);

                    // Handler penampil gambar lama/baru
                    if(feature.properties.image) {
                        $('#preview-image').attr('src', "{{ asset('storage/images') }}/" + feature.properties.image);
                    } else {
                        $('#preview-image').attr('src', '');
                    }

                    // Setup link action dinamis form ke update rute controller Laravel
                    var updateRoute = "{{ route('polyline.update', ':id') }}".replace(':id', feature.properties.id);
                    $('#formEditGeometry').attr('action', updateRoute);

                    // Buat Popup informasi detail rute di dalam peta
                    var popup_content = "<b>Nama Garis:</b> " + feature.properties.name + "<br>" +
                        "<b>Deskripsi:</b> " + feature.properties.description;
                    if(feature.properties.image) {
                        popup_content += "<br><img src='{{ asset('storage/images') }}/" + feature.properties.image + "' width='150px' class='mt-2'>";
                    }
                    layer.bindPopup(popup_content);
                }
            });

            // Tambahkan rute ke map utama
            polylines.addTo(map);

            // Fokuskan kamera dan lakukan auto-zoom tepat pada garis target
            if (data.features.length > 0) {
                var bounds = polylines.getBounds();
                map.fitBounds(bounds, {
                    padding: [50, 50],
                    maxZoom: 16
                });
            }
        });
    </script>
@endsection
