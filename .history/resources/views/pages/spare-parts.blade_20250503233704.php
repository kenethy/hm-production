@extends('layouts.main')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gray-900 text-white py-20">
    <div class="absolute inset-0 overflow-hidden">
        <img src="{{ asset('images/sparepart/sparepart.png') }}" alt="Sparepart Hartono Motor"
            class="w-full h-full object-cover opacity-40">
    </div>
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-3xl">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Sparepart Mobil</h1>
            <p class="text-xl">Kami menyediakan berbagai sparepart berkualitas untuk semua jenis mobil.</p>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-4">Kategori Sparepart</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Temukan sparepart berkualitas untuk kebutuhan kendaraan Anda.</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
            <!-- Category 1 -->
            <a href="#mesin" class="bg-gray-50 rounded-lg p-6 text-center hover:shadow-md transition-shadow">
                <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="font-bold">Mesin</h3>
            </a>

            <!-- Category 2 -->
            <a href="#rem" class="bg-gray-50 rounded-lg p-6 text-center hover:shadow-md transition-shadow">
                <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="font-bold">Rem</h3>
            </a>

            <!-- Category 3 -->
            <a href="#suspensi" class="bg-gray-50 rounded-lg p-6 text-center hover:shadow-md transition-shadow">
                <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                    </svg>
                </div>
                <h3 class="font-bold">Suspensi</h3>
            </a>

            <!-- Category 4 -->
            <a href="#elektrikal" class="bg-gray-50 rounded-lg p-6 text-center hover:shadow-md transition-shadow">
                <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="font-bold">Elektrikal</h3>
            </a>

            <!-- Category 5 -->
            <a href="#oli" class="bg-gray-50 rounded-lg p-6 text-center hover:shadow-md transition-shadow">
                <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </div>
                <h3 class="font-bold">Oli & Cairan</h3>
            </a>

            <!-- Category 6 -->
            <a href="#aksesoris" class="bg-gray-50 rounded-lg p-6 text-center hover:shadow-md transition-shadow">
                <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                </div>
                <h3 class="font-bold">Aksesoris</h3>
            </a>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section id="mesin" class="py-16 bg-gray-100">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-4">Produk Unggulan</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Sparepart berkualitas dengan harga terbaik.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Product 1 -->
            <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                <img src="{{ asset('images/product-1.jpg') }}" alt="Oli Mesin" class="w-full h-48 object-cover">
                <div class="p-6">
                    <span
                        class="inline-block bg-red-100 text-red-600 text-xs font-medium px-2 py-1 rounded-full mb-2">Oli
                        & Cairan</span>
                    <h3 class="text-xl font-bold mb-2">Shell Helix Ultra 5W-40</h3>
                    <p class="text-gray-600 mb-4">Oli mesin sintetis untuk performa optimal dan perlindungan mesin.</p>
                    <div class="flex justify-between items-center">
                        <span class="text-red-600 font-bold">Rp 450.000</span>
                        <div class="flex space-x-2">
                            <a href="https://www.tokopedia.com/hartonomotor" target="_blank"
                                class="bg-gray-100 hover:bg-gray-200 p-2 rounded-full transition-colors">
                                <img src="{{ asset('images/tokopedia-icon.png') }}" alt="Tokopedia" class="h-6 w-6">
                            </a>
                            <a href="https://shopee.co.id/hartonomotor" target="_blank"
                                class="bg-gray-100 hover:bg-gray-200 p-2 rounded-full transition-colors">
                                <img src="{{ asset('images/shopee-icon.png') }}" alt="Shopee" class="h-6 w-6">
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product 2 -->
            <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                <img src="{{ asset('images/product-2.jpg') }}" alt="Aki Mobil" class="w-full h-48 object-cover">
                <div class="p-6">
                    <span
                        class="inline-block bg-red-100 text-red-600 text-xs font-medium px-2 py-1 rounded-full mb-2">Elektrikal</span>
                    <h3 class="text-xl font-bold mb-2">GS Astra NS60L</h3>
                    <p class="text-gray-600 mb-4">Aki mobil dengan performa tinggi dan daya tahan lama.</p>
                    <div class="flex justify-between items-center">
                        <span class="text-red-600 font-bold">Rp 850.000</span>
                        <div class="flex space-x-2">
                            <a href="https://www.tokopedia.com/hartonomotor" target="_blank"
                                class="bg-gray-100 hover:bg-gray-200 p-2 rounded-full transition-colors">
                                <img src="{{ asset('images/tokopedia-icon.png') }}" alt="Tokopedia" class="h-6 w-6">
                            </a>
                            <a href="https://shopee.co.id/hartonomotor" target="_blank"
                                class="bg-gray-100 hover:bg-gray-200 p-2 rounded-full transition-colors">
                                <img src="{{ asset('images/shopee-icon.png') }}" alt="Shopee" class="h-6 w-6">
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product 3 -->
            <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                <img src="{{ asset('images/product-3.jpg') }}" alt="Kampas Rem" class="w-full h-48 object-cover">
                <div class="p-6">
                    <span
                        class="inline-block bg-red-100 text-red-600 text-xs font-medium px-2 py-1 rounded-full mb-2">Rem</span>
                    <h3 class="text-xl font-bold mb-2">Brembo Brake Pad</h3>
                    <p class="text-gray-600 mb-4">Kampas rem berkualitas tinggi untuk pengereman optimal dan aman.</p>
                    <div class="flex justify-between items-center">
                        <span class="text-red-600 font-bold">Rp 750.000</span>
                        <div class="flex space-x-2">
                            <a href="https://www.tokopedia.com/hartonomotor" target="_blank"
                                class="bg-gray-100 hover:bg-gray-200 p-2 rounded-full transition-colors">
                                <img src="{{ asset('images/tokopedia-icon.png') }}" alt="Tokopedia" class="h-6 w-6">
                            </a>
                            <a href="https://shopee.co.id/hartonomotor" target="_blank"
                                class="bg-gray-100 hover:bg-gray-200 p-2 rounded-full transition-colors">
                                <img src="{{ asset('images/shopee-icon.png') }}" alt="Shopee" class="h-6 w-6">
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product 4 -->
            <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                <img src="{{ asset('images/product-4.jpg') }}" alt="Filter Udara" class="w-full h-48 object-cover">
                <div class="p-6">
                    <span
                        class="inline-block bg-red-100 text-red-600 text-xs font-medium px-2 py-1 rounded-full mb-2">Mesin</span>
                    <h3 class="text-xl font-bold mb-2">K&N Air Filter</h3>
                    <p class="text-gray-600 mb-4">Filter udara performa tinggi untuk aliran udara optimal dan performa
                        mesin lebih baik.</p>
                    <div class="flex justify-between items-center">
                        <span class="text-red-600 font-bold">Rp 650.000</span>
                        <div class="flex space-x-2">
                            <a href="https://www.tokopedia.com/hartonomotor" target="_blank"
                                class="bg-gray-100 hover:bg-gray-200 p-2 rounded-full transition-colors">
                                <img src="{{ asset('images/tokopedia-icon.png') }}" alt="Tokopedia" class="h-6 w-6">
                            </a>
                            <a href="https://shopee.co.id/hartonomotor" target="_blank"
                                class="bg-gray-100 hover:bg-gray-200 p-2 rounded-full transition-colors">
                                <img src="{{ asset('images/shopee-icon.png') }}" alt="Shopee" class="h-6 w-6">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-10">
            <a href="https://www.tokopedia.com/hartonomotor" target="_blank"
                class="inline-block bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-md transition-colors">Lihat
                Semua Produk</a>
        </div>
    </div>
</section>

<!-- Brands Section -->
<section id="rem" class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-4">Merek Terpercaya</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Kami bekerja sama dengan merek-merek terpercaya untuk menjamin
                kualitas produk.</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-8">
            <div class="flex items-center justify-center p-4">
                <img src="{{ asset('images/brand-1.png') }}" alt="Brand 1"
                    class="h-16 opacity-70 hover:opacity-100 transition-opacity">
            </div>
            <div class="flex items-center justify-center p-4">
                <img src="{{ asset('images/brand-2.png') }}" alt="Brand 2"
                    class="h-16 opacity-70 hover:opacity-100 transition-opacity">
            </div>
            <div class="flex items-center justify-center p-4">
                <img src="{{ asset('images/brand-3.png') }}" alt="Brand 3"
                    class="h-16 opacity-70 hover:opacity-100 transition-opacity">
            </div>
            <div class="flex items-center justify-center p-4">
                <img src="{{ asset('images/brand-4.png') }}" alt="Brand 4"
                    class="h-16 opacity-70 hover:opacity-100 transition-opacity">
            </div>
            <div class="flex items-center justify-center p-4">
                <img src="{{ asset('images/brand-5.png') }}" alt="Brand 5"
                    class="h-16 opacity-70 hover:opacity-100 transition-opacity">
            </div>
            <div class="flex items-center justify-center p-4">
                <img src="{{ asset('images/brand-6.png') }}" alt="Brand 6"
                    class="h-16 opacity-70 hover:opacity-100 transition-opacity">
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-gray-100">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-lg shadow-md p-8 md:p-12">
            <div class="text-center max-w-3xl mx-auto">
                <h2 class="text-3xl font-bold mb-4">Tidak Menemukan Sparepart yang Anda Cari?</h2>
                <p class="text-gray-600 mb-8">Kami memiliki jaringan supplier yang luas dan dapat membantu Anda
                    menemukan sparepart yang dibutuhkan. Hubungi kami untuk informasi lebih lanjut.</p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="{{ route('contact') }}"
                        class="bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-md transition-colors">Hubungi
                        Kami</a>
                    <a href="https://wa.me/6281234567890" target="_blank"
                        class="bg-green-500 hover:bg-green-600 text-white font-medium py-3 px-6 rounded-md transition-colors flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="currentColor"
                            viewBox="0 0 24 24">
                            <path
                                d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                        </svg>
                        WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection