@props([
    'promos',
])

@if($promos->count() > 0)
<div class="bg-black text-white rounded-lg p-6 md:p-8 mb-16">
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
        @foreach($promos as $promo)
        <div class="bg-gray-900 rounded-lg overflow-hidden flex flex-col md:flex-row">
            <div class="md:w-1/3 relative">
                @if($promo->image_path)
                    <img src="{{ asset('storage/' . $promo->image_path) }}" alt="{{ $promo->title }}" class="w-full h-40 md:h-full object-cover">
                @else
                    <div class="w-full h-40 md:h-full bg-gray-800 flex items-center justify-center">
                        <span class="text-gray-500">No Image</span>
                    </div>
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
@endif
