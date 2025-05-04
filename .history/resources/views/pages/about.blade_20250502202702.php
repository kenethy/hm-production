@extends('layouts.main')

@section('content')
    <!-- Hero Section -->
    <section class="relative bg-gray-900 text-white py-20">
        <div class="absolute inset-0 overflow-hidden">
            <img src="{{ asset('images/about-bg.jpg') }}" alt="Tentang Hartono Motor" class="w-full h-full object-cover opacity-40">
        </div>
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-3xl">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Tentang Kami</h1>
                <p class="text-xl">Mengenal lebih dekat Hartono Motor, bengkel mobil terpercaya di Sidoarjo.</p>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-bold mb-6">Sejarah Hartono Motor</h2>
                    <p class="text-gray-600 mb-4">Hartono Motor didirikan pada tahun 2009 oleh Bapak Hartono, seorang wirausaha berpengalaman dengan passion di bidang otomotif. Berawal dari hanya menjual sparepart di Porong Tahun 1979, Hartono Motor terus berkembang hingga menjadi bengkel terpercaya di Sidoarjo.</p>
                    <p class="text-gray-600 mb-4">Selama lebih dari 15 tahun, kami telah melayani ribuan pelanggan dan menangani berbagai jenis kendaraan. Komitmen kami pada kualitas layanan dan kepuasan pelanggan menjadi kunci kesuksesan kami.</p>
                    <p class="text-gray-600">Saat ini, Hartono Motor telah berkembang menjadi bengkel modern dengan fasilitas lengkap dan tim mekanik profesional yang siap memberikan layanan terbaik untuk kendaraan Anda.</p>
                </div>
                <div>
                    <img src="{{ asset('images/kami/kami.jpg') }}" alt="Sejarah Hartono Motor" class="rounded-lg shadow-md w-full">
                </div>
            </div>
        </div>
    </section>

    <!-- Vision Mission Section -->
    <section class="py-16 bg-gray-100">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4">Visi & Misi</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Komitmen kami untuk memberikan layanan terbaik.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Vision -->
                <div class="bg-white p-8 rounded-lg shadow-md">
                    <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-center">Visi</h3>
                    <p class="text-gray-600 text-center">Menjadi bengkel mobil terpercaya dan terdepan di Sidoarjo dengan layanan berkualitas tinggi dan kepuasan pelanggan yang maksimal.</p>
                </div>
                
                <!-- Mission -->
                <div class="bg-white p-8 rounded-lg shadow-md">
                    <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-center">Misi</h3>
                    <ul class="text-gray-600 space-y-2">
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 mr-2 mt-0.5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>Memberikan layanan servis dan perbaikan mobil dengan kualitas terbaik.</span>
                        </li>
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 mr-2 mt-0.5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>Menyediakan sparepart berkualitas dengan harga yang kompetitif.</span>
                        </li>
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 mr-2 mt-0.5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>Mengembangkan tim mekanik yang profesional dan terampil.</span>
                        </li>
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 mr-2 mt-0.5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>Memberikan pengalaman pelanggan yang memuaskan dan membangun hubungan jangka panjang.</span>
                        </li>
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 mr-2 mt-0.5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>Mengadopsi teknologi terbaru untuk meningkatkan kualitas layanan.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4">Tim Kami</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Kenali tim profesional di balik layanan Hartono Motor.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Team Member 1 -->
                <div class="bg-gray-50 rounded-lg overflow-hidden shadow-md text-center">
                    <img src="{{ asset('images/team-1.jpg') }}" alt="Hartono" class="w-full h-64 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-1">Hartono</h3>
                        <p class="text-red-600 mb-4">Founder & CEO</p>
                        <p class="text-gray-600">Mekanik berpengalaman dengan lebih dari 20 tahun di industri otomotif.</p>
                    </div>
                </div>
                
                <!-- Team Member 2 -->
                <div class="bg-gray-50 rounded-lg overflow-hidden shadow-md text-center">
                    <img src="{{ asset('images/team-2.jpg') }}" alt="Budi Santoso" class="w-full h-64 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-1">Budi Santoso</h3>
                        <p class="text-red-600 mb-4">Kepala Mekanik</p>
                        <p class="text-gray-600">Spesialis mesin dengan sertifikasi dari berbagai produsen mobil terkemuka.</p>
                    </div>
                </div>
                
                <!-- Team Member 3 -->
                <div class="bg-gray-50 rounded-lg overflow-hidden shadow-md text-center">
                    <img src="{{ asset('images/team-3.jpg') }}" alt="Dewi Anggraini" class="w-full h-64 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-1">Dewi Anggraini</h3>
                        <p class="text-red-600 mb-4">Customer Service</p>
                        <p class="text-gray-600">Profesional dengan fokus pada kepuasan pelanggan dan komunikasi yang efektif.</p>
                    </div>
                </div>
                
                <!-- Team Member 4 -->
                <div class="bg-gray-50 rounded-lg overflow-hidden shadow-md text-center">
                    <img src="{{ asset('images/team-4.jpg') }}" alt="Rudi Hermawan" class="w-full h-64 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-1">Rudi Hermawan</h3>
                        <p class="text-red-600 mb-4">Spesialis Elektrikal</p>
                        <p class="text-gray-600">Ahli dalam sistem elektrikal dan elektronik mobil modern.</p>
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
                    <h2 class="text-3xl font-bold mb-4">Siap Untuk Servis Mobil Anda?</h2>
                    <p class="text-gray-600 mb-8">Percayakan perawatan mobil Anda kepada tim profesional Hartono Motor. Booking servis sekarang atau hubungi kami untuk informasi lebih lanjut.</p>
                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                        <a href="{{ route('booking') }}" class="bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-md transition-colors">Booking Servis</a>
                        <a href="{{ route('contact') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-900 font-medium py-3 px-6 rounded-md transition-colors">Hubungi Kami</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
