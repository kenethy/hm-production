<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        return view('pages.booking', [
            'title' => 'Booking'
        ]);
    }

    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'car_model' => 'required|string|max:255',
            'service_type' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'required|string',
            'message' => 'nullable|string',
        ]);

        // Store the booking in the database
        Booking::create($validated);

        return redirect()->route('booking')->with('success', 'Booking berhasil dibuat. Kami akan menghubungi Anda untuk konfirmasi.');
    }
}
