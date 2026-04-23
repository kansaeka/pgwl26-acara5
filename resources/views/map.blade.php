@extends('layouts.template')

@section('styles')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Leaflet Draw CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        /* Map height = full screen - navbar */
        #map {
            height: calc(100vh - 60px);
            width: 100%;
        }
    </style>
@endsection

@section('content')
    <div id="map"></div>

    {{-- Modal form Input Point --}}
    <div class="modal" tabindex="-1" id="modalInputPoint">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Input Point</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('points.store') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Fill name here...">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="geometry_point" class="form-label">Geometry</label>
                            <textarea class="form-control" id="geometry_point" name="geometry_point" rows="3"></textarea>
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

    {{-- Modal form Input Polyline --}}
    <div class="modal" tabindex="-1" id="modalInputPolyline">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Input Polyline</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('polylines.store') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Fill name here...">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="geometry_polylines" class="form-label">Geometry</label>
                            <textarea class="form-control" id="geometry_polylines" name="geometry_polylines" rows="3"></textarea>
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

    {{-- Modal form Input Polygon --}}
    <div class="modal" tabindex="-1" id="modalInputPolygon">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Input Polygon</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('polygons.store') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Fill name here...">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="geometry_polygons" class="form-label">Geometry</label>
                            <textarea class="form-control" id="geometry_polygons" name="geometry_polygons" rows="3"></textarea>
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
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Leaflet Draw JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
    <!-- Terraformer -->
    <script src="https://unpkg.com/@terraformer/wkt"></script>
    <!-- JQUERY -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        // Inisialisasi peta
        var map = L.map('map').setView([-7.7956, 110.3695], 10);

        // Basemap OSM
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
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

        map.on('draw:created', function(e) {
            var type = e.layerType,
                layer = e.layer;

            console.log(type);

            var drawnJSONObject = layer.toGeoJSON();
            var objectGeometry = Terraformer.geojsonToWKT(drawnJSONObject.geometry);

            console.log(drawnJSONObject);
            console.log(objectGeometry);

            if (type === 'polyline') {
                //set value geometry to geometry_input textarea
                $('#geometry_polylines').val(objectGeometry);

                //show modal input polyline
                $('#modalInputPolyline').modal('show');

                //Modal dismiss reload page
                $('#modalInputPolyline').on('hidden.bs.modal', function() {
                    location.reload();
                });

            } else if (type === 'polygon' || type === 'rectangle') {
                //set value geometry to geometry_input textarea
                $('#geometry_polygons').val(objectGeometry);

                //show modal input polygon
                $('#modalInputPolygon').modal('show');

                //Modal dismiss reload page
                $('#modalInputPolygon').on('hidden.bs.modal', function() {
                    location.reload();
                });

            } else if (type === 'marker') {
                //set value geometry to geometry_input textarea
                $('#geometry_point').val(objectGeometry);

                //show modal input point
                $('#modalInputPoint').modal('show');

                //Modal dismiss reload page
                $('#modalInputPoint').on('hidden.bs.modal', function() {
                    location.reload();
                });
            } else {
                console.log('undefined');
            }

            drawnItems.addLayer(layer);
        });


        // GeoJSON Point
        var points = L.geoJSON(null, {
            onEachFeature: function(feature, layer) {
                var popup_content =
                    "Nama: " + feature.properties.name + "<br>" +
                    "Deskripsi: " + feature.properties.description + "<br>" +
                    "Dibuat: " + feature.properties.created_at + "<br>" +
                    "Diupdate: " + feature.properties.updated_at + "<br>";

                layer.bindPopup(popup_content);
            }
        });

        $.getJSON("{{ route('geojson.points') }}", function(data) {
            points.addData(data);
            map.addLayer(points);
        });

        // GeoJSON Polyline
        var polylines = L.geoJSON(null, {
            onEachFeature: function(feature, layer) {
                var popup_content =
                    "Nama: " + feature.properties.name + "<br>" +
                    "Deskripsi: " + feature.properties.description + "<br>" +
                    "Dibuat: " + feature.properties.created_at + "<br>" +
                    "Diupdate: " + feature.properties.updated_at + "<br>";

                layer.bindPopup(popup_content);
            }
        });

        $.getJSON("{{ route('geojson.polylines') }}", function(data) {
            polylines.addData(data);
            map.addLayer(polylines);
        });

        // GeoJSON Polygon
        var polygons = L.geoJSON(null, {
            onEachFeature: function(feature, layer) {
                var popup_content =
                    "Nama: " + feature.properties.name + "<br>" +
                    "Deskripsi: " + feature.properties.description + "<br>" +
                    "Dibuat: " + feature.properties.created_at + "<br>" +
                    "Diupdate: " + feature.properties.updated_at + "<br>";

                layer.bindPopup(popup_content);
            }
        });

        $.getJSON("{{ route('geojson.polygons') }}", function(data) {
            polygons.addData(data);
            map.addLayer(polygons);
        });

        // Control Layer
        var baseMaps = {

        };

        var overlayMaps = {
            "Points": points,
            "Polylines": polylines,
            "Polygons": polygons,
        };

        var controllayer = L.control.layers(baseMaps, overlayMaps);
        controllayer.addTo(map);
    </script>
@endsection
