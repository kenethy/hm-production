@extends('layouts.main')

@section('title', 'Promo Spesial - Hartono Motor')

@section('content')
<!-- Debugging Section (Only visible in development) -->
@if(config('app.debug'))
<div class="bg-gray-800 text-white p-4 mb-4">
    <h2 class="text-xl font-bold mb-2">Debug Info</h2>
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-gray-700 p-3 rounded">
            <h3 class="font-bold">Total Promos</h3>
            <p>{{ $debug['total_promos'] }}</p>
        </div>
        <div class="bg-gray-700 p-3 rounded">
            <h3 class="font-bold">Featured</h3>
            <p>{{ $debug['featured_count'] }}</p>
        </div>
        <div class="bg-gray-700 p-3 rounded">
            <h3 class="font-bold">Active</h3>
            <p>{{ $debug['active_count'] }}</p>
        </div>
        <div class="bg-gray-700 p-3 rounded">
            <h3 class="font-bold">Ending Soon</h3>
            <p>{{ $debug['ending_soon_count'] }}</p>
        </div>
        <div class="bg-gray-700 p-3 rounded">
            <h3 class="font-bold">Limited Slots</h3>
            <p>{{ $debug['limited_slots_count'] }}</p>
        </div>
    </div>

    <div class="mt-4">
        <h3 class="font-bold mb-2">All Promos in Database:</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($allPromos as $promo)
            <div class="bg-gray-700 p-3 rounded text-sm">
                <p><strong>ID:</strong> {{ $promo->id }}</p>
                <p><strong>Title:</strong> {{ $promo->title }}</p>
                <p><strong>Featured:</strong> {{ $promo->is_featured ? 'Yes' : 'No' }}</p>
                <p><strong>Active:</strong> {{ $promo->is_active ? 'Yes' : 'No' }}</p>
                <p><strong>Start:</strong> {{ $promo->start_date->format('Y-m-d') }}</p>
                <p><strong>End:</strong> {{ $promo->end_date->format('Y-m-d') }}</p>
                <p><strong>Slots:</strong> {{ $promo->remaining_slots ?? 'Unlimited' }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif
<div class="bg-black text-white py-10">
    <div class="container mx-auto px-4">
        <!-- Hero Section -->
        <div class="relative overflow-hidden rounded-lg mb-12">
            <div class="absolute inset-0 bg-gradient-to-r from-red-600 to-red-900 opacity-90"></div>
            <div class="relative z-10 py-16 px-8 md:py-24 md:px-12">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">PROMO SPESIAL HARTONO MOTOR</h1>
                <p class="text-xl md:text-2xl mb-8">Dapatkan penawaran terbaik untuk perawatan kendaraan Anda!</p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="#limited-slots"
                        class="bg-white text-red-600 hover:bg-gray-200 font-bold py-3 px-6 rounded-full inline-flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                clip-rule="evenodd" />
                        </svg>
                        Slot Terbatas
                    </a>
                    <a href="#ending-soon"
                        class="bg-red-600 text-white hover:bg-red-700 font-bold py-3 px-6 rounded-full inline-flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                clip-rule="evenodd" />
                        </svg>
                        Segera Berakhir
                    </a>
                </div>
            </div>
        </div>

        <!-- Featured Promos -->
        @if($featuredPromos->count() > 0)
        <div class="mb-16">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold">PROMO UNGGULAN</h2>
                <div class="hidden md:block">
                    <span class="inline-block bg-red-600 text-white px-4 py-2 rounded-full animate-pulse">
                        Jangan Sampai Kehabisan!
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($featuredPromos as $promo)
                <div
                    class="bg-gray-900 rounded-lg overflow-hidden shadow-lg transform transition duration-300 hover:scale-105">
                    <div class="relative">
                        @if($promo->image_path)
                        <img src="{{ asset('storage/' . $promo->image_path) }}" alt="{{ $promo->title }}"
                            class="w-full h-48 object-cover">
                        @else
                        <div class="w-full h-48 bg-gray-800 flex items-center justify-center">
                            <span class="text-gray-500">No Image</span>
                        </div>
                        @endif

                        <div
                            class="absolute top-0 right-0 bg-red-600 text-white px-3 py-1 m-2 rounded-full text-sm font-bold">
                            UNGGULAN
                        </div>

                        @if($promo->is_ending_soon)
                        <div
                            class="absolute bottom-0 left-0 bg-yellow-500 text-black px-3 py-1 m-2 rounded-full text-sm font-bold animate-pulse">
                            Berakhir Dalam {{ $promo->remaining_time }}
                        </div>
                        @endif
                    </div>

                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2">{{ $promo->title }}</h3>
                        <p class="text-gray-400 mb-4">{{ Str::limit($promo->description, 100) }}</p>

                        <div class="flex justify-between items-center mb-4">
                            @if($promo->original_price)
                            <div>
                                <span class="text-gray-500 line-through">Rp {{ number_format($promo->original_price, 0,
                                    ',', '.') }}</span>
                                <span class="text-white font-bold text-xl block">Rp {{
                                    number_format($promo->promo_price, 0, ',', '.') }}</span>
                            </div>
                            <div class="bg-red-600 text-white px-3 py-1 rounded-full">
                                {{ $promo->discount_percentage }}% OFF
                            </div>
                            @endif
                        </div>

                        @if($promo->has_limited_slots)
                        <div class="mb-4">
                            <div class="flex justify-between mb-1">
                                <span class="text-sm text-gray-400">Sisa Slot:</span>
                                <span class="text-sm text-white font-bold">{{ $promo->remaining_slots }} tersisa</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2">
                                <div class="bg-red-600 h-2 rounded-full"
                                    style="width: {{ min(100, max(10, ($promo->remaining_slots / 20) * 100)) }}%"></div>
                            </div>
                        </div>
                        @endif

                        <a href="{{ route('promos.show', $promo->id) }}"
                            class="block w-full bg-red-600 hover:bg-red-700 text-white text-center font-bold py-2 px-4 rounded-full transition duration-300">
                            LIHAT DETAIL
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Ending Soon Promos -->
        @if($endingSoonPromos->count() > 0)
        <div id="ending-soon" class="mb-16">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold">SEGERA BERAKHIR</h2>
                <div class="hidden md:block">
                    <span class="inline-block bg-yellow-500 text-black px-4 py-2 rounded-full animate-pulse">
                        Jangan Sampai Menyesal!
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($endingSoonPromos as $promo)
                <div class="bg-gray-900 rounded-lg overflow-hidden shadow-lg border border-yellow-500">
                    <div class="relative">
                        @if($promo->image_path)
                        <img src="{{ asset('storage/' . $promo->image_path) }}" alt="{{ $promo->title }}"
                            class="w-full h-40 object-cover">
                        @else
                        <div class="w-full h-40 bg-gray-800 flex items-center justify-center">
                            <span class="text-gray-500">No Image</span>
                        </div>
                        @endif

                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-4">
                            <div
                                class="bg-yellow-500 text-black px-3 py-1 rounded-full text-sm font-bold inline-block animate-pulse">
                                Berakhir {{ $promo->remaining_time }}
                            </div>
                        </div>
                    </div>

                    <div class="p-4">
                        <h3 class="text-lg font-bold mb-2">{{ $promo->title }}</h3>

                        @if($promo->original_price)
                        <div class="flex justify-between items-center mb-3">
                            <div>
                                <span class="text-gray-500 line-through text-sm">Rp {{
                                    number_format($promo->original_price, 0, ',', '.') }}</span>
                                <span class="text-white font-bold block">Rp {{ number_format($promo->promo_price, 0,
                                    ',', '.') }}</span>
                            </div>
                            <div class="bg-red-600 text-white px-2 py-1 rounded-full text-sm">
                                {{ $promo->discount_percentage }}% OFF
                            </div>
                        </div>
                        @endif

                        <a href="{{ route('promos.show', $promo->id) }}"
                            class="block w-full bg-yellow-500 hover:bg-yellow-600 text-black text-center font-bold py-2 px-4 rounded-full transition duration-300 text-sm">
                            AMBIL SEKARANG
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Limited Slots Promos -->
        @if($limitedSlotPromos->count() > 0)
        <div id="limited-slots" class="mb-16">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold">SLOT TERBATAS</h2>
                <div class="hidden md:block">
                    <span class="inline-block bg-white text-red-600 px-4 py-2 rounded-full animate-pulse">
                        Cepat Sebelum Habis!
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($limitedSlotPromos as $promo)
                <div class="bg-gray-900 rounded-lg overflow-hidden shadow-lg border border-white">
                    <div class="relative">
                        @if($promo->image_path)
                        <img src="{{ asset('storage/' . $promo->image_path) }}" alt="{{ $promo->title }}"
                            class="w-full h-40 object-cover">
                        @else
                        <div class="w-full h-40 bg-gray-800 flex items-center justify-center">
                            <span class="text-gray-500">No Image</span>
                        </div>
                        @endif

                        <div
                            class="absolute top-0 right-0 bg-white text-red-600 px-3 py-1 m-2 rounded-full text-sm font-bold">
                            {{ $promo->remaining_slots }} SLOT
                        </div>
                    </div>

                    <div class="p-4">
                        <h3 class="text-lg font-bold mb-2">{{ $promo->title }}</h3>

                        @if($promo->original_price)
                        <div class="flex justify-between items-center mb-3">
                            <div>
                                <span class="text-gray-500 line-through text-sm">Rp {{
                                    number_format($promo->original_price, 0, ',', '.') }}</span>
                                <span class="text-white font-bold block">Rp {{ number_format($promo->promo_price, 0,
                                    ',', '.') }}</span>
                            </div>
                            <div class="bg-red-600 text-white px-2 py-1 rounded-full text-sm">
                                {{ $promo->discount_percentage }}% OFF
                            </div>
                        </div>
                        @endif

                        <div class="mb-3">
                            <div class="flex justify-between mb-1">
                                <span class="text-sm text-gray-400">Sisa Slot:</span>
                                <span class="text-sm text-white font-bold">{{ $promo->remaining_slots }} tersisa</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2">
                                <div class="bg-white h-2 rounded-full"
                                    style="width: {{ min(100, max(10, ($promo->remaining_slots / 20) * 100)) }}%"></div>
                            </div>
                        </div>

                        <a href="{{ route('promos.show', $promo->id) }}"
                            class="block w-full bg-white hover:bg-gray-200 text-red-600 text-center font-bold py-2 px-4 rounded-full transition duration-300 text-sm">
                            BOOKING SEKARANG
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- All Promos -->
        <div class="mb-16">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold">SEMUA PROMO</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($promos as $promo)
                <div class="bg-gray-900 rounded-lg overflow-hidden shadow-lg">
                    <div class="relative">
                        @if($promo->image_path)
                        <img src="{{ asset('storage/' . $promo->image_path) }}" alt="{{ $promo->title }}"
                            class="w-full h-48 object-cover">
                        @else
                        <div class="w-full h-48 bg-gray-800 flex items-center justify-center">
                            <span class="text-gray-500">No Image</span>
                        </div>
                        @endif

                        @if($promo->is_featured)
                        <div
                            class="absolute top-0 right-0 bg-red-600 text-white px-3 py-1 m-2 rounded-full text-sm font-bold">
                            UNGGULAN
                        </div>
                        @endif

                        @if($promo->is_ending_soon)
                        <div
                            class="absolute bottom-0 left-0 bg-yellow-500 text-black px-3 py-1 m-2 rounded-full text-sm font-bold">
                            Berakhir {{ $promo->remaining_time }}
                        </div>
                        @endif
                    </div>

                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2">{{ $promo->title }}</h3>
                        <p class="text-gray-400 mb-4">{{ Str::limit($promo->description, 80) }}</p>

                        @if($promo->original_price)
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <span class="text-gray-500 line-through">Rp {{ number_format($promo->original_price, 0,
                                    ',', '.') }}</span>
                                <span class="text-white font-bold text-xl block">Rp {{
                                    number_format($promo->promo_price, 0, ',', '.') }}</span>
                            </div>
                            <div class="bg-red-600 text-white px-3 py-1 rounded-full">
                                {{ $promo->discount_percentage }}% OFF
                            </div>
                        </div>
                        @endif

                        @if($promo->has_limited_slots)
                        <div class="mb-4">
                            <div class="flex justify-between mb-1">
                                <span class="text-sm text-gray-400">Sisa Slot:</span>
                                <span class="text-sm text-white font-bold">{{ $promo->remaining_slots }} tersisa</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2">
                                <div class="bg-red-600 h-2 rounded-full"
                                    style="width: {{ min(100, max(10, ($promo->remaining_slots / 20) * 100)) }}%"></div>
                            </div>
                        </div>
                        @endif

                        <a href="{{ route('promos.show', $promo->id) }}"
                            class="block w-full bg-red-600 hover:bg-red-700 text-white text-center font-bold py-2 px-4 rounded-full transition duration-300">
                            LIHAT DETAIL
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $promos->links() }}
            </div>
        </div>

        <!-- CTA Section -->
        <div class="bg-gradient-to-r from-red-600 to-red-900 rounded-lg p-8 md:p-12 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">JANGAN LEWATKAN KESEMPATAN INI!</h2>
            <p class="text-xl mb-8">Dapatkan layanan terbaik untuk kendaraan Anda dengan harga spesial.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('booking') }}"
                    class="bg-white text-red-600 hover:bg-gray-200 font-bold py-3 px-8 rounded-full inline-flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                            clip-rule="evenodd" />
                    </svg>
                    BOOKING SEKARANG
                </a>
                <a href="https://wa.me/6281234567890" target="_blank"
                    class="bg-green-500 text-white hover:bg-green-600 font-bold py-3 px-8 rounded-full inline-flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z"
                            clip-rule="evenodd" />
                    </svg>
                    TANYA VIA WHATSAPP
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Add countdown timer for ending soon promos
    document.addEventListener('DOMContentLoaded', function () {
        // Add some animation for FOMO effect
        const animateElements = document.querySelectorAll('.animate-pulse');
        setInterval(() => {
            animateElements.forEach(el => {
                el.classList.toggle('scale-105');
            });
        }, 2000);
    });
</script>
@endsection