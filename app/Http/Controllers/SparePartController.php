<?php

namespace App\Http\Controllers;

class SparePartController extends Controller
{
    public function index()
    {
        return view('pages.spare-parts', [
            'title' => 'Sparepart'
        ]);
    }
}
