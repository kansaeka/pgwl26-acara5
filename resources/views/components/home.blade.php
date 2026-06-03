@extends('layouts.template')

@section('styles')
    <style>
        body {
            background-color: #f5f7fb;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .card-header {
            background: #0d6efd;
            color: white;
            font-weight: 600;
            border-radius: 12px 12px 0 0;
        }

        thead {
            background-color: #e9f2ff;
        }

        th {
            text-align: center;
            font-weight: 600;
        }

        td {
            vertical-align: middle;
        }

        tbody tr:hover {
            background-color: #f1f7ff;
            transition: 0.2s;
        }
    </style>
@endsection

@section('content')
    <!-- Content -->
    <div class="container mt-4">

        <div class="card">
            <div class="card-header">
                <h3 class="mb-0">Aplikasi Web Sederhana</h3>
            </div>

            <div class="card-body">
                <p style="text-align: justify;">
                    Aplikasi ini merupakan pendekatan atas pemenuhan tugas mata kuliah Praktikum Pemrograman Geospasial
                    Website Lanjut. Aplikasi ini menampilkan Peta Interaktif yang berisikan objek geometri berupa titik,
                    garis, dan area. Setiap bagian dari data tersebut dapat dilakukan pendekatan CRUD di dalam setiap isinya
                    sehingga dapat menjadi gambaran penggunaan website dalam analisis spasial. Aplikasi ini dikembangkan
                    dengan menggunakan framework Laravel dan PostgreSQL dengan ekstensi PostGIS sebagai basis data
                    spasialnya. Aplikasi ini juga menggunakan Leaflet JS sebagai library untuk menampilkan peta interaktif
                    di dalam website.
                </p>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-3">
                <div class="card">
                    <div class="card-header">
                        <h3>Jumlah Poin</h3>
                    </div>
                    <div class="card-body text-center">
                        <h1>
                            {{ $point_count }}
                        </h1>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card">
                    <div class="card-header">
                        <h3>Jumlah Polyline</h3>
                    </div>
                    <div class="card-body text-center">
                        <h1>
                            {{ $polyline_count }}
                        </h1>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card">
                    <div class="card-header">
                        <h3>Jumlah Polygon</h3>
                    </div>
                    <div class="card-body text-center">
                        <h1>
                            {{ $polygon_count }}
                        </h1>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card">
                    <div class="card-header">
                        <h3>Jumlah User</h3>
                    </div>
                    <div class="card-body text-center">
                        <h1>
                            {{ $user_count }}
                        </h1>
                    </div>
                </div>
            </div>

        </div>

    </div>
    </div>
@endsection
