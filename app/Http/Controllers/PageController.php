<?php

namespace App\Http\Controllers;

use App\Models\pointsModel;
use App\Models\polylinesModel;
use App\Models\polygonModel;
use App\Models\User;
use Illuminate\Http\Request;

class PageController extends Controller
{

public function __construct()
    {
        $this->point = new pointsModel();
        $this->polyline = new polylinesModel();
        $this->polygon = new polygonModel();
        $this->user = new User();
    }

public function landingpage()
    {
        $data = [
            'title' => 'Petakans',
            'point_count' => $this->point->count(),
            'polyline_count' => $this->polyline->count(),
            'polygon_count' => $this->polygon->count(),
            'user_count' => $this->user->count(),
        ];

        return view('home', $data);
    }

    public function peta()
    {
        $data = [
            'title' => 'Peta',
        ];

        return view('map', $data);
    }

    public function tabel()
    {
        $data = [
            'title' => 'Tabel',
            'points' => $this->point->all(),
            'polylines' => $this->polyline->all(),
            'polygons' => $this->polygon->all(),

        ];

        return view('table', $data);
    }
}
