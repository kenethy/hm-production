<?php

namespace App\Http\Controllers;

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
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'car_model' => 'required|string|max:255',
            'service_type' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'required|string',
            'message' => 'nullable|string',
        ]);
        
        // Here you would typically store the booking in the database
        // For now, we'll just redirect with a success message
        
        return redirect()->route('booking')->with('success', 'Booking berhasil dibuat. Kami akan menghubungi Anda untuk konfirmasi.');
    }
}
