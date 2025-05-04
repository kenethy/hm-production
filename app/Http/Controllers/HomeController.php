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
            'title' => 'Bengkel Mobil Terpercaya di Sidoarjo',
            'metaDescription' => 'Hartono Motor - Bengkel mobil terpercaya di Sidoarjo dengan layanan servis berkualitas, sparepart lengkap, dan mekanik berpengalaman. Booking servis online sekarang!',
            'metaKeywords' => 'bengkel mobil sidoarjo, servis mobil, sparepart mobil, tune up mesin, ganti oli, servis ac mobil, bengkel terpercaya, hartono motor',
            'ogImage' => asset('images/hero-bg.png'),
            'featuredPromos' => $featuredPromos,
            'endingSoonPromos' => $endingSoonPromos
        ]);
    }
}
