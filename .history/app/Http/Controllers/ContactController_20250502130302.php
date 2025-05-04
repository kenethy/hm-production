<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return view('pages.contact', [
            'title' => 'Kontak'
        ]);
    }
    
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);
        
        // Here you would typically store the contact message in the database
        // and/or send an email notification
        
        // For now, we'll just redirect with a success message
        return redirect()->route('contact')->with('success', 'Pesan Anda telah terkirim. Kami akan menghubungi Anda segera.');
    }
}
