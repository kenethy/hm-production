<?php

namespace App\Http\Controllers;

use App\Models\Promo;

class HomeController extends Controller
{
    public function index()
    {
        return view('pages.home', [
            'title' => 'Beranda'
        ]);
    }
}
