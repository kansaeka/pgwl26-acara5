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
                <h3 class="mb-0">Tabel Data</h3>
            </div>

            <div class="card-body">

                <table class="table table-bordered table-striped table-hover align-middle">

                    <thead>
                        <tr>
                            <th style="width:60px;">No</th>
                            <th>Nama</th>
                            <th>Deskripsi</th>
                            <th style="width:120px;">Gambar</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr>
                            <td class="text-center">1</td>
                            <td>Bundaran UGM</td>
                            <td>Jl. Cik Di Tiro, Sagan, Caturtunggal, Kec. Depok, Kabupaten Sleman</td>
                            <td class="text-center">-</td>
                        </tr>

                        <tr>
                            <td class="text-center">2</td>
                            <td>Gunung Merapi</td>
                            <td>Hargobinangun, Pakem, Kabupaten Sleman</td>
                            <td class="text-center">-</td>
                        </tr>

                        <tr>
                            <td class="text-center">3</td>
                            <td>Tugu Yogyakarta</td>
                            <td>Jl. Jend. Sudirman, Gowongan, Kec. Jetis, Kota Yogyakarta</td>
                            <td class="text-center">-</td>
                        </tr>

                        <tr>
                            <td class="text-center">4</td>
                            <td>Jalan Malioboro</td>
                            <td>Sosromenduran, Gedong Tengen, Kota Yogyakarta</td>
                            <td class="text-center">-</td>
                        </tr>

                        <tr>
                            <td class="text-center">5</td>
                            <td>Alun-alun Utara</td>
                            <td>Gondomanan, Kota Yogyakarta</td>
                            <td class="text-center">-</td>
                        </tr>

                    </tbody>

                </table>

            </div>
        </div>

    </div>
    @endsection
