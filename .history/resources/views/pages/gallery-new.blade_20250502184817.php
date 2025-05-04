@extends('layouts.main')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gray-900 text-white py-20">
    <div class="absolute inset-0 overflow-hidden">
        <img src="{{ asset('images/hero-bg.png') }}" alt="Galeri Hartono Motor"
            class="w-full h-full object-cover object-top opacity-40">
    </div>
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-3xl">
            <h1 class="text-4xl md:text-5xl font-bold mb-4 animate-fade-in">Galeri</h1>
            <p class="text-xl animate-slide-up delay-200">Lihat koleksi foto bengkel, hasil servis, dan aktivitas kami.
            </p>
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
        @if($featuredItems->count() > 0 && $activeCategory === 'all')
        <!-- Featured Gallery Items -->
        <div class="mb-12">
            <h2 class="text-2xl font-bold mb-6 text-center">Foto Unggulan</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($featuredItems as $item)
                <div
                    class="overflow-hidden rounded-lg shadow-md hover:shadow-lg transition-shadow hover-lift reveal-up">
                    <a href="{{ route('gallery.show', $item->id) }}" class="block relative group">
                        <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}"
                            class="w-full h-64 object-cover">
                        <div
                            class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                            <div class="text-white text-center p-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                </svg>
                                <h3 class="font-bold text-lg">{{ $item->title }}</h3>
                                <p class="text-sm">{{ $item->category->name }}</p>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- All Gallery Items -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($galleryItems as $item)
            <div class="overflow-hidden rounded-lg shadow-md hover:shadow-lg transition-shadow hover-lift reveal-up">
                <a href="{{ route('gallery.show', $item->id) }}" class="block relative group">
                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}"
                        class="w-full h-64 object-cover">
                    <div
                        class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                        <div class="text-white text-center p-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                            </svg>
                            <h3 class="font-bold text-lg">{{ $item->title }}</h3>
                            <p class="text-sm">{{ $item->category->name }}</p>
                        </div>
                    </div>
                </a>
            </div>
            @empty
            <div class="col-span-3 py-12 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="text-xl font-bold mb-2">Belum Ada Foto</h3>
                <p class="text-gray-600">Belum ada foto yang tersedia untuk kategori ini.</p>
            </div>
            @endforelse
        </div>

        @if($galleryItems->hasPages())
        <div class="mt-10 flex justify-center">
            {{ $galleryItems->links() }}
        </div>
        @endif
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
                        class="bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-md transition-colors btn-animate">Booking
                        Servis</a>
                    <a href="{{ route('contact') }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-900 font-medium py-3 px-6 rounded-md transition-colors btn-animate">Hubungi
                        Kami</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection