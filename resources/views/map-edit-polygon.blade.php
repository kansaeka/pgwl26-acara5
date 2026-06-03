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
                    <h5 class="modal-title">Edit Data Polygon (Area)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditGeometry" action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Fill name">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="geometry" class="form-label">Geometry (POLYGON)</label>
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
        // Set view default
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

        map.on('draw:edited', function (e) {
            var layers = e.layers;

            layers.eachLayer(function (layer) {
                var drawnJSONObject = layer.toGeoJSON();
                var objectGeometry = Terraformer.geojsonToWKT(drawnJSONObject.geometry);
                var properties = drawnJSONObject.properties;

                // Memasukkan data ke input modal
                $('#name').val(properties.name);
                $('#description').val(properties.description);
                $('#geometry').val(objectGeometry);
                $('#preview-image').attr('src', "{{ asset('storage/images') }}/" + properties.image);

                // SESUAIKAN DI SINI: Mengubah target route ke rute update milik polygon Anda
                var updateRoute = "{{ route('polygon.update', ':id') }}".replace(':id', properties.id);
                // Sesuai dengan nama route: route('polygon.update') di web.php
                $('#formEditGeometry').attr('action', updateRoute);

                // Menampilkan modal edit
                $('#modalEdit').modal('show');
            });
        });

        // SESUAIKAN DI SINI: Inisialisasi khusus GeoJSON Polygon (Area)
        var polygons = L.geoJSON(null, {
            style: function (feature) {
                return {
                    color: "#4a148c",       // Warna garis tepi area (Ungu Tua)
                    weight: 3,              // Ketebalan garis tepi
                    fillColor: "#8e24aa",   // Warna isian dalam area (Ungu Muda)
                    fillOpacity: 0.4        // Transparansi warna isian (0-1)
                };
            },
            onEachFeature: function (feature, layer) {
                drawnItems.addLayer(layer);

                var popup_content = "<b>Nama Area:</b> " + feature.properties.name + "<br>" +
                                    "<b>Deskripsi:</b> " + feature.properties.description + "<br>" +
                                    "<img src='{{ asset('storage/images') }}/" + feature.properties.image + "' width='150px' class='mt-2'>";

                layer.bindPopup(popup_content);
            },
        });

        // SESUAIKAN DI SINI: Mengambil data GeoJSON tunggal polygon dari ApiController
        $.getJSON("{{ route('geojson.polygons', $id) }}", function(data) {

            var polygons = L.geoJSON(data, {
                style: function(feature) {
                    return {
                        color: "#3388ff", // Warna garis tepi polygon
                        fillColor: "#3388ff",
                        weight: 3,
                        opacity: 0.8,
                        fillOpacity: 0.2
                    };
                },
                onEachFeature: function(feature, layer) {
                    // Masukkan ke grup Leaflet Draw agar tombol "Edit" aktif bawaan Leaflet Draw
                    drawnItems.addLayer(layer);

                    // PENTING: Pasang property bawaan database ke objek layer Leaflet
                    layer.properties = feature.properties;

                    // Mengisi nilai awal input text modal secara otomatis saat pertama dibuka
                    $('#name').val(feature.properties.name);
                    $('#description').val(feature.properties.description);

                    // Konversi koordinat ke format text WKT POLYGON untuk textarea geometry
                    var wkt = Terraformer.geojsonToWKT(feature.geometry);
                    $('#geometry').val(wkt);

                    // Handler penampil gambar
                    if(feature.properties.image) {
                        $('#preview-image').attr('src', "{{ asset('storage/images') }}/" + feature.properties.image);
                    } else {
                        $('#preview-image').attr('src', '');
                    }

                    // Tempel url route update dinamis ke form action (Pastikan namanya sesuai route update Anda)
                    var updateRoute = "{{ route('polygon.update', ':id') }}".replace(':id', feature.properties.id);
                    $('#formEditGeometry').attr('action', updateRoute);

                    // Buat popup info di dalam peta
                    var popup_content = "<b>Nama Area:</b> " + feature.properties.name + "<br>" +
                        "<b>Deskripsi:</b> " + feature.properties.description;
                    if(feature.properties.image) {
                        popup_content += "<br><img src='{{ asset('storage/images') }}/" + feature.properties.image + "' width='150px' class='mt-2'>";
                    }
                    layer.bindPopup(popup_content);
                }
            });

            polygons.addTo(map);

            // AUTO ZOOM FOKUS HANYA PADA AREA POLYGON INI
            if (data.features.length > 0) {
                var bounds = polygons.getBounds();
                map.fitBounds(bounds, {
                    padding: [50, 50],
                    maxZoom: 16 // Supaya tidak terlalu dekat beneran dekat zoomnya
                });
            }
        });
    </script>
@endsection
