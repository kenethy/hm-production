<?php

namespace App\Http\Controllers;

use App\Models\Promo;

class HomeController extends Controller
{
    public function index()
    {
        // Get featured promos
        $featuredPromos = Promo::active()->featured()->latest()->take(3)->get();

        // Get ending soon promos
        $endingSoonPromos = Promo::active()
            ->whereRaw('DATEDIFF(end_date, NOW()) < 3')
            ->whereRaw('DATEDIFF(end_date, NOW()) >= 0')
            ->latest()
            ->take(2)
            ->get();

        return view('pages.home', [
            'title' => 'Beranda',
            'featuredPromos' => $featuredPromos,
            'endingSoonPromos' => $endingSoonPromos
        ]);
    }
}
