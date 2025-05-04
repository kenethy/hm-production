@props([
    'featuredPromos',
    'endingSoonPromos',
])

<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-4">PROMO SPESIAL</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Dapatkan penawaran terbaik untuk perawatan kendaraan Anda!</p>
            <div class="mt-4">
                <a href="{{ route('promos') }}" class="inline-flex items-center text-red-600 font-bold hover:text-red-700 transition-colors">
                    LIHAT SEMUA PROMO
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </a>
            </div>
        </div>

        @if($featuredPromos->count() > 0)
        <!-- Featured Promos -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            @foreach($featuredPromos as $promo)
            <div class="bg-gray-50 rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-all transform hover:-translate-y-1">
                <div class="relative">
                    @if($promo->image_path)
                        <img src="{{ asset('storage/' . $promo->image_path) }}" alt="{{ $promo->title }}" class="w-full h-48 object-cover">
                    @else
                        <img src="{{ asset('images/promo-default.jpg') }}" alt="{{ $promo->title }}" class="w-full h-48 object-cover">
                    @endif
                    
                    @if($promo->discount_percentage)
                    <div class="absolute top-4 right-4 bg-red-600 text-white py-1 px-3 rounded-full font-bold">
                        HEMAT {{ $promo->discount_percentage }}%
                    </div>
                    @endif
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2">{{ $promo->title }}</h3>
                    <p class="text-gray-600 mb-4">{{ Str::limit($promo->description, 100) }}</p>
                    
                    @if($promo->original_price)
                    <div class="mb-4">
                        <span class="text-gray-500 line-through">Rp {{ number_format($promo->original_price, 0, ',', '.') }}</span>
                        <span class="text-red-600 font-bold text-xl block">Rp {{ number_format($promo->promo_price, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    
                    <div class="flex justify-between items-center">
                        <p class="text-red-600 font-bold text-sm">Berakhir: {{ $promo->end_date->format('d M Y') }}</p>
                        <a href="{{ route('promos.show', $promo->id) }}" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-full transition-colors">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        @if($endingSoonPromos->count() > 0)
        <!-- Ending Soon Promos -->
        <div class="bg-black text-white rounded-lg p-6 md:p-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div>
                    <h3 class="text-2xl font-bold mb-2">PROMO SEGERA BERAKHIR!</h3>
                    <p class="text-gray-300">Jangan sampai ketinggalan penawaran spesial ini</p>
                </div>
                <a href="{{ route('promos') }}#ending-soon" class="mt-4 md:mt-0 inline-flex items-center bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full transition-colors">
                    LIHAT SEMUA
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($endingSoonPromos as $promo)
                <div class="bg-gray-900 rounded-lg overflow-hidden flex flex-col md:flex-row">
                    <div class="md:w-1/3 relative">
                        @if($promo->image_path)
                            <img src="{{ asset('storage/' . $promo->image_path) }}" alt="{{ $promo->title }}" class="w-full h-40 md:h-full object-cover">
                        @else
                            <img src="{{ asset('images/promo-default.jpg') }}" alt="{{ $promo->title }}" class="w-full h-40 md:h-full object-cover">
                        @endif
                        
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-2">
                            <div class="bg-yellow-500 text-black px-2 py-1 rounded-full text-xs font-bold inline-block animate-pulse">
                                Berakhir {{ $promo->remaining_time }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="md:w-2/3 p-4">
                        <h4 class="font-bold text-lg mb-2">{{ $promo->title }}</h4>
                        
                        @if($promo->original_price)
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-gray-400 line-through text-sm">Rp {{ number_format($promo->original_price, 0, ',', '.') }}</span>
                            <span class="bg-red-600 text-white px-2 py-0.5 rounded-full text-xs">
                                {{ $promo->discount_percentage }}% OFF
                            </span>
                        </div>
                        <div class="text-white font-bold mb-3">
                            Rp {{ number_format($promo->promo_price, 0, ',', '.') }}
                        </div>
                        @endif
                        
                        <a href="{{ route('promos.show', $promo->id) }}" class="inline-block bg-yellow-500 hover:bg-yellow-600 text-black text-center font-bold py-1 px-4 rounded-full transition-colors text-sm">
                            AMBIL SEKARANG
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <!-- Default Promos -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Promo 1 -->
            <div class="bg-gray-50 rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                <div class="relative">
                    <img src="{{ asset('images/promo-1.jpg') }}" alt="Promo Servis Berkala"
                        class="w-full h-64 object-cover">
                    <div class="absolute top-4 right-4 bg-red-600 text-white py-1 px-3 rounded-full font-bold">Hemat 20%
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-2xl font-bold mb-2">Paket Servis Berkala</h3>
                    <p class="text-gray-600 mb-4">Dapatkan diskon 20% untuk paket servis berkala 10.000 km. Termasuk
                        penggantian oli, filter oli, dan pemeriksaan 20 komponen penting.</p>
                    <div class="flex justify-between items-center">
                        <p class="text-red-600 font-bold">Berlaku hingga: 30 Juni 2023</p>
                        <a href="{{ route('booking') }}"
                            class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md transition-colors">Booking
                            Sekarang</a>
                    </div>
                </div>
            </div>

            <!-- Promo 2 -->
            <div class="bg-gray-50 rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                <div class="relative">
                    <img src="{{ asset('images/promo-2.jpg') }}" alt="Promo AC Service"
                        class="w-full h-64 object-cover">
                    <div class="absolute top-4 right-4 bg-red-600 text-white py-1 px-3 rounded-full font-bold">Hemat 15%
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-2xl font-bold mb-2">Paket Servis AC</h3>
                    <p class="text-gray-600 mb-4">Diskon 15% untuk servis AC lengkap. Termasuk pengecekan kebocoran,
                        pengisian freon, dan pembersihan filter AC.</p>
                    <div class="flex justify-between items-center">
                        <p class="text-red-600 font-bold">Berlaku hingga: 31 Juli 2023</p>
                        <a href="{{ route('booking') }}"
                            class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md transition-colors">Booking
                            Sekarang</a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>
