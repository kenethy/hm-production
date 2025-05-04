@extends('layouts.main')

@section('content')
    <!-- Hero Section -->
    <section class="relative bg-gray-900 text-white">
        <div class="absolute inset-0 overflow-hidden">
            <img src="{{ asset('images/hero-bg.jpg') }}" alt="Hartono Motor Workshop" class="w-full h-full object-cover opacity-40">
        </div>
        <div class="container mx-auto px-4 py-24 relative z-10">
            <div class="max-w-3xl">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Hartono Motor: Solusi Servis & Sparepart Mobil Terlengkap di Sidoarjo</h1>
                <p class="text-xl mb-8">Melayani berbagai merek dan jenis mobil dengan sparepart lengkap dan mekanik berpengalaman.</p>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('booking') }}" class="bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-md transition-colors">Booking Servis Sekarang</a>
                    <a href="{{ route('services') }}" class="bg-white hover:bg-gray-100 text-gray-900 font-medium py-3 px-6 rounded-md transition-colors">Lihat Layanan Kami</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Layanan Unggulan Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4">Layanan Unggulan</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Kami menyediakan berbagai layanan perawatan dan perbaikan untuk menjaga kendaraan Anda tetap prima.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Service 1 -->
                <div class="bg-gray-50 p-6 rounded-lg text-center hover:shadow-lg transition-shadow">
                    <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Servis Berkala</h3>
                    <p class="text-gray-600">Perawatan rutin untuk menjaga performa dan memperpanjang usia kendaraan Anda.</p>
                </div>
                
                <!-- Service 2 -->
                <div class="bg-gray-50 p-6 rounded-lg text-center hover:shadow-lg transition-shadow">
                    <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Tune Up Mesin</h3>
                    <p class="text-gray-600">Optimalkan performa mesin dengan penyetelan dan perawatan komprehensif.</p>
                </div>
                
                <!-- Service 3 -->
                <div class="bg-gray-50 p-6 rounded-lg text-center hover:shadow-lg transition-shadow">
                    <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Servis AC</h3>
                    <p class="text-gray-600">Perbaikan dan perawatan sistem AC untuk kenyamanan berkendara Anda.</p>
                </div>
                
                <!-- Service 4 -->
                <div class="bg-gray-50 p-6 rounded-lg text-center hover:shadow-lg transition-shadow">
                    <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Ganti Oli</h3>
                    <p class="text-gray-600">Penggantian oli berkualitas untuk menjaga kesehatan mesin kendaraan Anda.</p>
                </div>
            </div>
            
            <div class="text-center mt-10">
                <a href="{{ route('services') }}" class="inline-block bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-md transition-colors">Lihat Semua Layanan</a>
            </div>
        </div>
    </section>
@endsection
