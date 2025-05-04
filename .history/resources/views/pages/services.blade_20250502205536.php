@extends('layouts.main')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gray-900 text-white py-20">
    <div class="absolute inset-0 overflow-hidden">
        <img src="{{ asset('images/services-bg.jpg') }}" alt="Layanan Hartono Motor"
            class="w-full h-full object-cover opacity-40" fetchpriority="high">
    </div>
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-3xl">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Layanan Kami</h1>
            <p class="text-xl">Kami menyediakan berbagai layanan perawatan dan perbaikan untuk menjaga kendaraan Anda
                tetap prima.</p>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Service 1 -->
            <div class="bg-gray-50 rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                <img src="{{ asset('images/service-1.jpg') }}" alt="Servis Berkala" class="w-full h-64 object-cover"
                    loading="lazy">
                <div class="p-6">
                    <h3 class="text-2xl font-bold mb-3">Servis Berkala</h3>
                    <p class="text-gray-600 mb-4">Perawatan rutin untuk menjaga performa dan memperpanjang usia
                        kendaraan Anda. Termasuk penggantian oli, filter oli, dan pemeriksaan komponen penting lainnya.
                    </p>
                    <div class="flex justify-between items-center">
                        <span class="text-red-600 font-bold">Mulai dari Rp 350.000</span>
                        <a href="{{ route('booking') }}"
                            class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md transition-colors">Booking</a>
                    </div>
                </div>
            </div>

            <!-- Service 2 -->
            <div class="bg-gray-50 rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                <img src="{{ asset('images/service-2.jpg') }}" alt="Tune Up Mesin" class="w-full h-64 object-cover"
                    loading="lazy">
                <div class="p-6">
                    <h3 class="text-2xl font-bold mb-3">Tune Up Mesin</h3>
                    <p class="text-gray-600 mb-4">Optimalkan performa mesin dengan penyetelan dan perawatan
                        komprehensif. Termasuk pembersihan throttle body, injector, dan penyetelan mesin.</p>
                    <div class="flex justify-between items-center">
                        <span class="text-red-600 font-bold">Mulai dari Rp 450.000</span>
                        <a href="{{ route('booking') }}"
                            class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md transition-colors">Booking</a>
                    </div>
                </div>
            </div>

            <!-- Service 3 -->
            <div class="bg-gray-50 rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                <img src="{{ asset('images/service-3.jpg') }}" alt="Servis AC" class="w-full h-64 object-cover"
                    loading="lazy">
                <div class="p-6">
                    <h3 class="text-2xl font-bold mb-3">Servis AC</h3>
                    <p class="text-gray-600 mb-4">Perbaikan dan perawatan sistem AC untuk kenyamanan berkendara Anda.
                        Termasuk pengecekan kebocoran, pengisian freon, dan pembersihan filter AC.</p>
                    <div class="flex justify-between items-center">
                        <span class="text-red-600 font-bold">Mulai dari Rp 400.000</span>
                        <a href="{{ route('booking') }}"
                            class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md transition-colors">Booking</a>
                    </div>
                </div>
            </div>

            <!-- Service 4 -->
            <div class="bg-gray-50 rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                <img src="{{ asset('images/service-4.jpg') }}" alt="Ganti Oli" class="w-full h-64 object-cover">
                <div class="p-6">
                    <h3 class="text-2xl font-bold mb-3">Ganti Oli</h3>
                    <p class="text-gray-600 mb-4">Penggantian oli berkualitas untuk menjaga kesehatan mesin kendaraan
                        Anda. Tersedia berbagai pilihan oli sesuai kebutuhan kendaraan Anda.</p>
                    <div class="flex justify-between items-center">
                        <span class="text-red-600 font-bold">Mulai dari Rp 250.000</span>
                        <a href="{{ route('booking') }}"
                            class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md transition-colors">Booking</a>
                    </div>
                </div>
            </div>

            <!-- Service 5 -->
            <div class="bg-gray-50 rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                <img src="{{ asset('images/service-5.jpg') }}" alt="Perbaikan Rem" class="w-full h-64 object-cover">
                <div class="p-6">
                    <h3 class="text-2xl font-bold mb-3">Perbaikan Rem</h3>
                    <p class="text-gray-600 mb-4">Perbaikan dan perawatan sistem rem untuk keamanan berkendara. Termasuk
                        penggantian kampas rem, cakram, dan pemeriksaan sistem rem.</p>
                    <div class="flex justify-between items-center">
                        <span class="text-red-600 font-bold">Mulai dari Rp 350.000</span>
                        <a href="{{ route('booking') }}"
                            class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md transition-colors">Booking</a>
                    </div>
                </div>
            </div>

            <!-- Service 6 -->
            <div class="bg-gray-50 rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                <img src="{{ asset('images/service-6.jpg') }}" alt="Balancing & Spooring"
                    class="w-full h-64 object-cover">
                <div class="p-6">
                    <h3 class="text-2xl font-bold mb-3">Balancing & Spooring</h3>
                    <p class="text-gray-600 mb-4">Penyeimbangan dan penyetelan roda untuk kenyamanan berkendara dan umur
                        ban yang lebih panjang. Termasuk pemeriksaan kaki-kaki kendaraan.</p>
                    <div class="flex justify-between items-center">
                        <span class="text-red-600 font-bold">Mulai dari Rp 300.000</span>
                        <a href="{{ route('booking') }}"
                            class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md transition-colors">Booking</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-gray-100">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-lg shadow-md p-8 md:p-12">
            <div class="text-center max-w-3xl mx-auto">
                <h2 class="text-3xl font-bold mb-4">Butuh Layanan Lain?</h2>
                <p class="text-gray-600 mb-8">Kami juga menyediakan berbagai layanan lain sesuai kebutuhan kendaraan
                    Anda. Hubungi kami untuk informasi lebih lanjut atau booking servis sekarang.</p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="{{ route('booking') }}"
                        class="bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-md transition-colors">Booking
                        Servis</a>
                    <a href="{{ route('contact') }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-900 font-medium py-3 px-6 rounded-md transition-colors">Hubungi
                        Kami</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection