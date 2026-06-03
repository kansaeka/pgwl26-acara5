@extends('layouts.template')

@section('styles')
    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    {{-- Leaflet Draw CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css">
    <style>
        /* Menghilangkan margin bawaan browser dan memastikan tinggi penuh */
        html,
        body {
            margin: 0;

        }

        /* Membuat peta memenuhi seluruh layar */
        #map {
            height: calc(100vh - 56px); /* Mengurangi tinggi navbar jika ada */
            width: 100%;
        }
    </style>
@endsection

@section('content')
    <div id="map"></div>
    {{-- modal form input point --}}
    <div class="modal" tabindex="-1" id="modalInputPoint">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Input Point</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('points.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
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
                            <label for="geometry_point" class="form-label">Geometry</label>
                            <textarea class="form-control" id="geometry_point" name="geometry_point" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input class="form-control" type="file" id="image" name="image"
                            onchange="document.getElementById('preview-image-point').src = window.URL.createObjectURL(this.files[0])">
                        </div>
                        <div class="mb-3">
                            <img src="" alt="" id="preview-image-point" class="img-thumbnail" width="400">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- modal form input polyline --}}
    <div class="modal" tabindex="-1" id="modalInputPolyline">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Input Polyline</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('polyline.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
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
                            <label for="geometry_polyline" class="form-label">Geometry</label>
                            <textarea class="form-control" id="geometry_polyline" name="geometry_polyline"
                                rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input class="form-control" type="file" id="image" name="image"
                            onchange="document.getElementById('preview-image-polyline').src = window.URL.createObjectURL(this.files[0])">
                        </div>
                        <div class="mb-3">
                            <img src="" alt="" id="preview-image-polyline" class="img-thumbnail" width="400">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- modal form input polygon-- --}}
    <div class="modal" tabindex="-1" id="modalInputPolygon">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Input Polygon</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('polygon.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
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
                            <label for="geometry_polygon" class="form-label">Geometry</label>
                            <textarea class="form-control" id="geometry_polygon" name="geometry_polygon"
                                rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input class="form-control" type="file" id="image" name="image"
                            onchange="document.getElementById('preview-image-polygon').src = window.URL.createObjectURL(this.files[0])">
                        </div>
                        <div class="mb-3">
                            <img src="" alt="" id="preview-image-polygon" class="img-thumbnail" width="400">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('components.map-navbar')
    {{-- Bootstrap JS Bundle (termasuk Popper) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <div id="map"></div>
    {{-- Leaflet JS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    {{-- Leaflet Draw JS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>

    {{-- Terraformer JS --}}
    <script src="https://unpkg.com/@terraformer/wkt"></script>
    {{-- jQuery JS--}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        // Inisialisasi peta
        var map = L.map('map').setView([-6.1754, 106.8272], 13);

        // Menambahkan basemap dari OpenStreetMap
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);
        /* Digitize Function */
        var drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        var drawControl = new L.Control.Draw({
            draw: {
                position: 'topleft',
                polyline: true,
                polygon: true,
                rectangle: true,
                circle: false,
                marker: true,
                circlemarker: false
            },
            edit: false
        });

        map.addControl(drawControl);

        map.on('draw:created', function (e) {
            var type = e.layerType;
            var layer = e.layer;

            // Tambahkan layer ke map
            drawnItems.addLayer(layer);

            // Ambil GeoJSON full (bukan hanya geometry)
            var geojson = layer.toGeoJSON();

            // Konversi ke WKT
            var wkt = Terraformer.geojsonToWKT(geojson.geometry);

            console.log(type);
            console.log(geojson);
            console.log(wkt);
            if (type === 'polygon' || type === 'rectangle') {

                // Pastikan hasil rectangle tetap polygon
                $('#geometry_polygon').val(wkt);

                // Hapus event lama biar tidak double
                $('#modalInputPolygon').off('hidden.bs.modal');

                // Tampilkan modal
                $('#modalInputPolygon').modal('show');

                // Reload setelah modal ditutup
                $('#modalInputPolygon').on('hidden.bs.modal', function () {
                    location.reload();
                });

            } else if (type === 'marker') {

                $('#geometry_point').val(wkt);

                $('#modalInputPoint').off('hidden.bs.modal');
                $('#modalInputPoint').modal('show');

                $('#modalInputPoint').on('hidden.bs.modal', function () {
                    location.reload();
                });

            } else if (type === 'polyline') {

                $('#geometry_polyline').val(wkt);

                $('#modalInputPolyline').off('hidden.bs.modal');
                $('#modalInputPolyline').modal('show');

                $('#modalInputPolyline').on('hidden.bs.modal', function () {
                    location.reload();
                });
            }
        });
        // GeoJSON Points
        var points = L.geoJSON(null, {
            // Style

            // onEachFeature
            onEachFeature: function (feature, layer) {
                //Route delete point
                var routedelete = "{{ route('points.delete', ':id') }}"; routedelete = routedelete.replace(':id', feature.properties.id);
                //Route edit point
                var routeedit = "{{ route('point.edit', ':id') }}"; routeedit = routeedit.replace(':id', feature.properties.id);
                // variable popup content
                var popup_content = "Nama: " + feature.properties.name + "<br>" +
                    "Deskripsi: " + feature.properties.description + "<br>" +
                    "Dibuat:" + feature.properties.created_at + "<br>" +
                    "<br><img src='{{ asset('storage/images') }}/" +
                    feature.properties.image + "' style='max-width:100%; height:auto;'>" +
                    "<br><br>" +
                    "<div class='row'>" +
                    "<div class='col-md-2'>" +
                    "<form action='"+routedelete+"' method='post'>" +'@csrf' +'@method("delete")' +
                    "<button type='submit' class='btn btn-sm btn-danger' title='Delete Point' onclick='return confirm(`sing genah?`)''><i class='fa fa-trash'></i></button>" +
                    "</form>" +
                    "</div>" +
                    "<div class='col-md-2'>" +
                    "<a href='"+routeedit+"' class='btn btn-warning btn-sm' title='Edit Point'><i class='far fa-edit'></i></a>" +
                    "</div>" +
                    "</div>"
                    ;

                layer.on({
                    click: function (e) {
                        points.bindPopup(popup_content);
                    },
                });
            },

        });
        $.getJSON("{{route('geojson.points')}} ",
            function (data) {
                points.addData(data);
                map.addLayer(points);
            });

        //GeoJSON Polyline
        var polyline = L.geoJSON(null, {
            onEachFeature: function(feature, layer) {
                // Route delete polyline (Sudah ada di kode Anda)
                var routedelete = "{{ route('polyline.delete', ':id') }}"; routedelete = routedelete.replace(':id', feature.properties.id);

                // Route edit polyline (BARU: Silakan tambahkan ini)
                var routeedit = "{{ route('polyline.edit', ':id') }}"; routeedit = routeedit.replace(':id', feature.properties.id);

                var imageHtml = "";
                if (feature.properties.image) {
                    imageHtml = "<br><img src='{{ asset('storage/images') }}/" + feature.properties.image + "' style='max-width:100%; height:auto;'>";
                }

                // Struktur popup diubah menggunakan col-md-2 agar tombol Hapus dan Edit berjejer (Sama seperti struktur Point Anda)
                var popup_content = "Nama: " + feature.properties.name + "<br>" +
                    "Deskripsi: " + feature.properties.description + "<br>" +
                    "Dibuat:" + feature.properties.created_at + "<br>" +
                    imageHtml + "<br><br>" +
                    "<div class='row'>" +
                        "<div class='col-md-2'>" +
                            "<form action='"+routedelete+"' method='post'>" +'@csrf' +'@method("delete")' +
                            "<button type='submit' class='btn btn-sm btn-danger' title='Delete Polyline' onclick='return confirm(`sing genah?`)''><i class='fa fa-trash'></i></button>" +
                            "</form>" +
                        "</div>" +
                        "<div class='col-md-2'>" +
                            // TOMBOL EDIT POLYLINE
                            "<a href='"+routeedit+"' class='btn btn-warning btn-sm' title='Edit Polyline'><i class='far fa-edit'></i></a>" +
                        "</div>" +
                    "</div>";

                layer.on({
                    click: function(e) {
                        polyline.bindPopup(popup_content);
                    },
                });
            },
        });

        $.getJSON("{{route('geojson.polylines') }}", function(data) {
            polyline.addData(data);
            map.addLayer(polyline);
        });

        // GeoJSON Polygon
        var polygon = L.geoJSON(null, {
            onEachFeature: function(feature, layer) {
                // Route delete polygon (Sudah ada di kode Anda)
                var routedelete = "{{ route('polygon.delete', ':id') }}"; routedelete = routedelete.replace(':id', feature.properties.id);

                // Route edit polygon (BARU: Tambahkan ini)
                var routeedit = "{{ route('polygon.edit', ':id') }}"; routeedit = routeedit.replace(':id', feature.properties.id);

                var imageHtml = "";
                if (feature.properties.image) {
                    imageHtml = "<br><img src='{{ asset('storage/images') }}/" + feature.properties.image + "' style='max-width:100%; height:auto;'>";
                }

                var popup_content = "Nama: " + feature.properties.name + "<br>" +
                    "Deskripsi: " + feature.properties.description + "<br>" +
                    "Dibuat:" + feature.properties.created_at + "<br>" +
                    imageHtml + "<br><br>" +
                    "<div class='row'>" +
                        "<div class='col-md-2'>" +
                            "<form action='"+routedelete+"' method='post'>" +'@csrf' +'@method("delete")' +
                            "<button type='submit' class='btn btn-sm btn-danger' title='Delete Polygon' onclick='return confirm(`Yakin ingin menghapus?`)''><i class='fa fa-trash'></i></button>" +
                            "</form>" +
                        "</div>" +
                        "<div class='col-md-2'>" +
                            // TOMBOL EDIT POLYGON (BARU)
                            "<a href='"+routeedit+"' class='btn btn-warning btn-sm' title='Edit Polygon'><i class='far fa-edit'></i></a>" +
                        "</div>" +
                    "</div>";

                layer.on({
                    click: function(e) {
                        polygon.bindPopup(popup_content);
                    },
                });
            },
        });

        $.getJSON("{{ route('geojson.polygons') }}", function(data) {
            polygon.addData(data);
            map.addLayer(polygon);
        });

        // Control Layer
        var baseMaps = {
        };

        var overlayMaps = {
            "Points": points,
            "Polyline": polyline,
            "Polygon": polygon,
        };

        var controllayer = L.control.layers(baseMaps, overlayMaps);
        controllayer.addTo(map);
    </script>

@endsection
