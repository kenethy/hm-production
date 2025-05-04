<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    /**
     * Display a listing of the promos.
     */
    public function index()
    {
        // Get featured promos
        $featuredPromos = Promo::where('is_featured', true)
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->latest()
            ->take(3)
            ->get();

        // Get all active promos
        $promos = Promo::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->latest()
            ->paginate(9);

        // Get ending soon promos (less than 3 days)
        $endingSoonPromos = Promo::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where('end_date', '<=', now()->addDays(3))
            ->latest()
            ->take(4)
            ->get();

        // Get limited slot promos
        $limitedSlotPromos = Promo::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->whereNotNull('remaining_slots')
            ->where('remaining_slots', '>', 0)
            ->latest()
            ->take(4)
            ->get();

        return view('promos.index-new', compact('featuredPromos', 'promos', 'endingSoonPromos', 'limitedSlotPromos'));
    }

    /**
     * Display the specified promo.
     */
    public function show($id)
    {
        $promo = Promo::findOrFail($id);

        // Get related promos
        $relatedPromos = Promo::active()
            ->where('id', '!=', $promo->id)
            ->latest()
            ->take(4)
            ->get();

        return view('promos.show-new', compact('promo', 'relatedPromos'));
    }
}
