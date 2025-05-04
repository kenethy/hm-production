@extends('layouts.main')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gray-900 text-white py-20">
    <div class="absolute inset-0 overflow-hidden">
        <img src="{{ asset('images/gallery-bg.jpg') }}" alt="Galeri Hartono Motor"
            class="w-full h-full object-cover opacity-40">
    </div>
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-3xl">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Galeri</h1>
            <p class="text-xl">Lihat koleksi foto bengkel, hasil servis, dan aktivitas kami.</p>
        </div>
    </div>
</section>

<!-- Gallery Categories -->
<section class="py-8 bg-white">
    <div class="container mx-auto px-4">
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('gallery') }}"
                class="{{ $activeCategory === 'all' ? 'bg-red-600 text-white' : 'bg-gray-200 hover:bg-gray-300 text-gray-800' }} px-4 py-2 rounded-md font-medium transition-colors">
                Semua
            </a>

            @foreach($categories as $category)
            <a href="{{ route('gallery', ['category' => $category->slug]) }}"
                class="{{ $activeCategory === $category->slug ? 'bg-red-600 text-white' : 'bg-gray-200 hover:bg-gray-300 text-gray-800' }} px-4 py-2 rounded-md font-medium transition-colors">
                {{ $category->name }}
            </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Gallery Grid -->
<section class="py-12 bg-white">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Gallery Item 1 -->
            <div class="overflow-hidden rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <a href="{{ asset('images/gallery-1.jpg') }}" class="block relative group">
                    <img src="{{ asset('images/gallery-1.jpg') }}" alt="Tampak Depan Bengkel"
                        class="w-full h-64 object-cover">
                    <div
                        class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                        <div class="text-white text-center p-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                            </svg>
                            <h3 class="font-bold text-lg">Tampak Depan Bengkel</h3>
                            <p class="text-sm">Bengkel</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Gallery Item 2 -->
            <div class="overflow-hidden rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <a href="{{ asset('images/gallery-2.jpg') }}" class="block relative group">
                    <img src="{{ asset('images/gallery-2.jpg') }}" alt="Area Servis" class="w-full h-64 object-cover">
                    <div
                        class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                        <div class="text-white text-center p-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                            </svg>
                            <h3 class="font-bold text-lg">Area Servis</h3>
                            <p class="text-sm">Bengkel</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Gallery Item 3 -->
            <div class="overflow-hidden rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <a href="{{ asset('images/gallery-3.jpg') }}" class="block relative group">
                    <img src="{{ asset('images/gallery-3.jpg') }}" alt="Mekanik Bekerja"
                        class="w-full h-64 object-cover">
                    <div
                        class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                        <div class="text-white text-center p-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                            </svg>
                            <h3 class="font-bold text-lg">Mekanik Bekerja</h3>
                            <p class="text-sm">Mekanik</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Gallery Item 4 -->
            <div class="overflow-hidden rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <a href="{{ asset('images/gallery-4.jpg') }}" class="block relative group">
                    <img src="{{ asset('images/gallery-4.jpg') }}" alt="Hasil Servis Mesin"
                        class="w-full h-64 object-cover">
                    <div
                        class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                        <div class="text-white text-center p-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                            </svg>
                            <h3 class="font-bold text-lg">Hasil Servis Mesin</h3>
                            <p class="text-sm">Hasil Servis</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Gallery Item 5 -->
            <div class="overflow-hidden rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <a href="{{ asset('images/gallery-5.jpg') }}" class="block relative group">
                    <img src="{{ asset('images/gallery-5.jpg') }}" alt="Rak Sparepart" class="w-full h-64 object-cover">
                    <div
                        class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                        <div class="text-white text-center p-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                            </svg>
                            <h3 class="font-bold text-lg">Rak Sparepart</h3>
                            <p class="text-sm">Sparepart</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Gallery Item 6 -->
            <div class="overflow-hidden rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <a href="{{ asset('images/gallery-6.jpg') }}" class="block relative group">
                    <img src="{{ asset('images/gallery-6.jpg') }}" alt="Ruang Tunggu" class="w-full h-64 object-cover">
                    <div
                        class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                        <div class="text-white text-center p-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                            </svg>
                            <h3 class="font-bold text-lg">Ruang Tunggu</h3>
                            <p class="text-sm">Bengkel</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Gallery Item 7 -->
            <div class="overflow-hidden rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <a href="{{ asset('images/gallery-7.jpg') }}" class="block relative group">
                    <img src="{{ asset('images/gallery-7.jpg') }}" alt="Hasil Servis AC"
                        class="w-full h-64 object-cover">
                    <div
                        class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                        <div class="text-white text-center p-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                            </svg>
                            <h3 class="font-bold text-lg">Hasil Servis AC</h3>
                            <p class="text-sm">Hasil Servis</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Gallery Item 8 -->
            <div class="overflow-hidden rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <a href="{{ asset('images/gallery-8.jpg') }}" class="block relative group">
                    <img src="{{ asset('images/gallery-8.jpg') }}" alt="Tim Mekanik" class="w-full h-64 object-cover">
                    <div
                        class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                        <div class="text-white text-center p-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                            </svg>
                            <h3 class="font-bold text-lg">Tim Mekanik</h3>
                            <p class="text-sm">Mekanik</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Gallery Item 9 -->
            <div class="overflow-hidden rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <a href="{{ asset('images/gallery-9.jpg') }}" class="block relative group">
                    <img src="{{ asset('images/gallery-9.jpg') }}" alt="Koleksi Sparepart"
                        class="w-full h-64 object-cover">
                    <div
                        class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                        <div class="text-white text-center p-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                            </svg>
                            <h3 class="font-bold text-lg">Koleksi Sparepart</h3>
                            <p class="text-sm">Sparepart</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="mt-10 text-center">
            <button
                class="bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-md transition-colors">Muat
                Lebih Banyak</button>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-gray-100">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-lg shadow-md p-8 md:p-12">
            <div class="text-center max-w-3xl mx-auto">
                <h2 class="text-3xl font-bold mb-4">Ingin Melihat Langsung?</h2>
                <p class="text-gray-600 mb-8">Kunjungi bengkel kami untuk melihat langsung fasilitas dan layanan yang
                    kami sediakan. Atau booking servis sekarang untuk pengalaman servis yang memuaskan.</p>
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