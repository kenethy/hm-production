@extends('layouts.main')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gray-900 text-white">
    <div class="absolute inset-0 overflow-hidden">
        <img src="{{ asset('images/hero-bg.png') }}" alt="Hartono Motor Workshop"
            class="w-full h-full object-cover opacity-40" style="object-position: center 30%;">
    </div>
    <div class="container mx-auto px-4 py-24 relative z-10">
        <div class="max-w-3xl">
            <h1 class="text-4xl md:text-5xl font-bold mb-4 animate-fade-in">Hartono Motor: Solusi Servis & Sparepart
                Mobil Terlengkap di
                Sidoarjo</h1>
            <p class="text-xl mb-8 animate-slide-up delay-200">Melayani berbagai merek dan jenis mobil dengan sparepart
                lengkap dan mekanik
                berpengalaman.</p>
            <div class="flex flex-wrap gap-4 animate-slide-up delay-400">
                <a href="{{ route('booking') }}"
                    class="bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-md transition-colors btn-animate">Booking
                    Servis Sekarang</a>
                <a href="{{ route('services') }}"
                    class="bg-white hover:bg-gray-100 text-gray-900 font-medium py-3 px-6 rounded-md transition-colors btn-animate">Lihat
                    Layanan Kami</a>
            </div>
        </div>
    </div>
</section>

<!-- Layanan Unggulan Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12 reveal">
            <h2 class="text-3xl font-bold mb-4">Layanan Unggulan</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Kami menyediakan berbagai layanan perawatan dan perbaikan untuk
                menjaga kendaraan Anda tetap prima.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Service 1 -->
            <div class="bg-gray-50 p-6 rounded-lg text-center hover-lift reveal-up">
                <div
                    class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse-subtle">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-2">Servis Berkala</h3>
                <p class="text-gray-600">Perawatan rutin untuk menjaga performa dan memperpanjang usia kendaraan Anda.
                </p>
            </div>

            <!-- Service 2 -->
            <div class="bg-gray-50 p-6 rounded-lg text-center hover-lift reveal-up" style="transition-delay: 0.1s">
                <div
                    class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse-subtle">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-2">Tune Up Mesin</h3>
                <p class="text-gray-600">Optimalkan performa mesin dengan penyetelan dan perawatan komprehensif.</p>
            </div>

            <!-- Service 3 -->
            <div class="bg-gray-50 p-6 rounded-lg text-center hover-lift reveal-up" style="transition-delay: 0.2s">
                <div
                    class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse-subtle">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-2">Servis AC</h3>
                <p class="text-gray-600">Perbaikan dan perawatan sistem AC untuk kenyamanan berkendara Anda.</p>
            </div>

            <!-- Service 4 -->
            <div class="bg-gray-50 p-6 rounded-lg text-center hover-lift reveal-up" style="transition-delay: 0.3s">
                <div
                    class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse-subtle">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-2">Ganti Oli</h3>
                <p class="text-gray-600">Penggantian oli berkualitas untuk menjaga kesehatan mesin kendaraan Anda.</p>
            </div>
        </div>

        <div class="text-center mt-10 reveal">
            <a href="{{ route('services') }}"
                class="inline-block bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-md transition-colors btn-animate">Lihat
                Semua Layanan</a>
        </div>
    </div>
</section>

<!-- Keunggulan Section -->
<section class="py-16 bg-gray-100">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-4">Keunggulan Hartono Motor</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Kami berkomitmen memberikan layanan terbaik dengan standar
                kualitas tinggi.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- USP 1 -->
            <div class="flex flex-col items-center">
                <div class="bg-white w-20 h-20 rounded-full flex items-center justify-center shadow-md mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-red-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-2 text-center">Sparepart Terlengkap</h3>
                <p class="text-gray-600 text-center">Menyediakan berbagai sparepart asli dan berkualitas untuk semua
                    jenis mobil.</p>
            </div>

            <!-- USP 2 -->
            <div class="flex flex-col items-center">
                <div class="bg-white w-20 h-20 rounded-full flex items-center justify-center shadow-md mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-red-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-2 text-center">Mekanik Berpengalaman</h3>
                <p class="text-gray-600 text-center">Tim teknisi profesional dengan pengalaman dan sertifikasi di
                    bidangnya.</p>
            </div>

            <!-- USP 3 -->
            <div class="flex flex-col items-center">
                <div class="bg-white w-20 h-20 rounded-full flex items-center justify-center shadow-md mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-red-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-2 text-center">Layanan Cepat & Profesional</h3>
                <p class="text-gray-600 text-center">Penanganan cepat dan efisien dengan hasil yang memuaskan.</p>
            </div>

            <!-- USP 4 -->
            <div class="flex flex-col items-center">
                <div class="bg-white w-20 h-20 rounded-full flex items-center justify-center shadow-md mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-red-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-2 text-center">Garansi Servis</h3>
                <p class="text-gray-600 text-center">Memberikan jaminan kualitas untuk setiap pekerjaan yang kami
                    lakukan.</p>
            </div>
        </div>
    </div>
</section>

<!-- Promo Section -->
<x-promos.home-section :featuredPromos="$featuredPromos" :endingSoonPromos="$endingSoonPromos" />

<!-- Testimonials Section -->
<section class="py-16 bg-gray-100">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-4 reveal">Testimoni Pelanggan</h2>
            <p class="text-gray-600 max-w-2xl mx-auto reveal-up">Apa kata pelanggan tentang layanan kami.</p>
        </div>

        <!-- Include the testimonial carousel component -->
        @include('components.testimonial-carousel')
    </div>
</section>

<!-- Marketplace & Social Media Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-4">Temukan Kami di</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Kunjungi toko online dan media sosial kami untuk informasi
                terbaru.</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-6 gap-6">
            <!-- Marketplace -->
            <a href="https://www.tokopedia.com/hartono-m" target="_blank"
                class="flex flex-col items-center p-4 rounded-lg hover:bg-gray-50 transition-colors">
                <img src="{{ asset('images/marketplace/tokopedia.png') }}" alt="Tokopedia"
                    class="h-16 w-16 object-contain mb-3">
                <span class="font-medium text-gray-900">Tokopedia</span>
            </a>

            <a href="https://shopee.co.id/hartono_motor" target="_blank"
                class="flex flex-col items-center p-4 rounded-lg hover:bg-gray-50 transition-colors">
                <img src="{{ asset('images/marketplace/shopee.png') }}" alt="Shopee"
                    class="h-16 w-16 object-contain mb-3">
                <span class="font-medium text-gray-900">Shopee</span>
            </a>

            <a href="https://www.lazada.co.id/shop/hartonomotor" target="_blank"
                class="flex flex-col items-center p-4 rounded-lg hover:bg-gray-50 transition-colors">
                <img src="{{ asset('images/marketplace/lazada.png') }}" alt="Lazada"
                    class="h-16 w-16 object-contain mb-3">
                <span class="font-medium text-gray-900">Lazada</span>
            </a>

            <!-- Social Media -->
            <a href="https://instagram.com/hartonomotorsidoarjo" target="_blank"
                class="flex flex-col items-center p-4 rounded-lg hover:bg-gray-50 transition-colors">
                <img src="{{ asset('images/marketplace/instagram.png') }}" alt="Instagram"
                    class="h-16 w-16 object-contain mb-3">
                <span class="font-medium text-gray-900">Instagram</span>
            </a>

            <a href="https://www.facebook.com/hartonomotorsidoarjo" target="_blank"
                class="flex flex-col items-center p-4 rounded-lg hover:bg-gray-50 transition-colors">
                <img src="{{ asset('images/marketplace/facebook.png') }}" alt="Facebook"
                    class="h-16 w-16 object-contain mb-3">
                <span class="font-medium text-gray-900">Facebook</span>
            </a>

            <a href="https://www.tiktok.com/@hartonomotorsidoarjo" target="_blank"
                class="flex flex-col items-center p-4 rounded-lg hover:bg-gray-50 transition-colors">
                <img src="{{ asset('images/marketplace/tiktok.png') }}" alt="TikTok"
                    class="h-16 w-16 object-contain mb-3">
                <span class="font-medium text-gray-900">TikTok</span>
            </a>
        </div>
    </div>
</section>

<!-- Map & Contact Section -->
<section class="py-16 bg-gray-100">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-4">Lokasi & Kontak</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Kunjungi bengkel kami atau hubungi kami untuk informasi lebih
                lanjut.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Map -->
            <div class="rounded-lg overflow-hidden shadow-md h-96">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d43371.58621648754!2d112.68514490118963!3d-7.4377883769144715!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7e6d2846c1cd3%3A0x15b5e7e7d101e4c3!2sHARTONO%20MOTOR%20Bengkel%20Mobil%2FSparepart%20Onderdil!5e0!3m2!1sid!2sid!4v1746187797124!5m2!1sid!2sid"
                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>

            <!-- Contact Info -->
            <div class="bg-white p-8 rounded-lg shadow-md">
                <h3 class="text-2xl font-bold mb-6">Informasi Kontak</h3>

                <div class="space-y-6">
                    <div class="flex items-start">
                        <div class="bg-red-100 rounded-full p-3 mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg mb-1">Alamat</h4>
                            <p class="text-gray-600">Jl. Samanhudi No 2, Kebonsari, Sidoarjo
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="bg-red-100 rounded-full p-3 mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg mb-1">Telepon</h4>
                            <p class="text-gray-600">+62 821 3520 2581</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="bg-red-100 rounded-full p-3 mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg mb-1">Email</h4>
                            <p class="text-gray-600">hartonomotor1979@gmail.com
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="bg-red-100 rounded-full p-3 mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg mb-1">Jam Operasional</h4>
                            <p class="text-gray-600">Senin - Sabtu: 08.00 - 16.00</p>
                            <p class="text-gray-600">Sabtu: 08.00 - 15.00</p>
                            <p class="text-gray-600">Minggu: Tutup</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <a href="{{ route('contact') }}"
                        class="inline-block bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-md transition-colors">Hubungi
                        Kami</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection