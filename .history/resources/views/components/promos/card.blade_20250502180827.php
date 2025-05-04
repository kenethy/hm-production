@props([
'promo',
'type' => 'default', // default, featured, ending-soon, limited-slots
'showDescription' => true,
])

<div class="bg-gray-900 rounded-lg overflow-hidden shadow-lg hover-lift reveal-up
    @if($type == 'featured') transform transition duration-300 hover:scale-105 @endif
    @if($type == 'ending-soon') border border-yellow-500 @endif
    @if($type == 'limited-slots') border border-white @endif">

    <div class="relative">
        @if($promo->image_path)
        <img src="{{ asset('storage/' . $promo->image_path) }}" alt="{{ $promo->title }}"
            class="w-full h-48 object-cover @if($type != 'featured') h-40 @endif">
        @else
        <div class="w-full h-48 bg-gray-800 flex items-center justify-center @if($type != 'featured') h-40 @endif">
            <span class="text-gray-500">No Image</span>
        </div>
        @endif

        {{-- Badges --}}
        @if($promo->is_featured && $type == 'default')
        <div class="absolute top-0 right-0 bg-red-600 text-white px-3 py-1 m-2 rounded-full text-sm font-bold">
            UNGGULAN
        </div>
        @endif

        @if($type == 'featured')
        <div class="absolute top-0 right-0 bg-red-600 text-white px-3 py-1 m-2 rounded-full text-sm font-bold">
            UNGGULAN
        </div>
        @endif

        @if($promo->is_ending_soon && ($type == 'default' || $type == 'featured'))
        <div
            class="absolute bottom-0 left-0 bg-yellow-500 text-black px-3 py-1 m-2 rounded-full text-sm font-bold animate-pulse">
            Berakhir {{ $promo->remaining_time }}
        </div>
        @endif

        @if($type == 'ending-soon')
        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-4">
            <div class="bg-yellow-500 text-black px-3 py-1 rounded-full text-sm font-bold inline-block animate-pulse">
                Berakhir {{ $promo->remaining_time }}
            </div>
        </div>
        @endif

        @if($type == 'limited-slots')
        <div class="absolute top-0 right-0 bg-white text-red-600 px-3 py-1 m-2 rounded-full text-sm font-bold">
            {{ $promo->remaining_slots }} SLOT
        </div>
        @endif
    </div>

    <div class="p-6 @if($type != 'featured') p-4 @endif">
        <h3 class="@if($type == 'featured') text-xl @else text-lg @endif font-bold mb-2">{{ $promo->title }}</h3>

        @if($showDescription)
        <p class="text-gray-400 mb-4">{{ Str::limit($promo->description, $type == 'featured' ? 100 : 80) }}</p>
        @endif

        @if($promo->original_price)
        <div class="flex justify-between items-center mb-4 @if($type != 'featured') mb-3 @endif">
            <div>
                <span class="text-gray-500 line-through @if($type != 'featured') text-sm @endif">
                    Rp {{ number_format($promo->original_price, 0, ',', '.') }}
                </span>
                <span class="text-white font-bold @if($type == 'featured') text-xl @endif block">
                    Rp {{ number_format($promo->promo_price, 0, ',', '.') }}
                </span>
            </div>
            <div class="bg-red-600 text-white px-3 py-1 rounded-full @if($type != 'featured') text-sm px-2 @endif">
                {{ $promo->discount_percentage }}% OFF
            </div>
        </div>
        @endif

        @if($promo->has_limited_slots && ($type == 'limited-slots' || $type == 'featured' || $type == 'default'))
        <div class="mb-4 @if($type != 'featured') mb-3 @endif">
            <div class="flex justify-between mb-1">
                <span class="text-sm text-gray-400">Sisa Slot:</span>
                <span class="text-sm text-white font-bold">{{ $promo->remaining_slots }} tersisa</span>
            </div>
            <div class="w-full bg-gray-700 rounded-full h-2">
                <div class="@if($type == 'limited-slots') bg-white @else bg-red-600 @endif h-2 rounded-full"
                    style="width: {{ min(100, max(10, ($promo->remaining_slots / 20) * 100)) }}%">
                </div>
            </div>
        </div>
        @endif

        <a href="{{ route('promos.show', $promo->id) }}" class="block w-full font-bold py-2 px-4 rounded-full transition-colors text-center btn-animate
            @if($type == 'ending-soon')
                bg-yellow-500 hover:bg-yellow-600 text-black text-sm animate-pulse-subtle
            @elseif($type == 'limited-slots')
                bg-white hover:bg-gray-200 text-red-600 text-sm
            @else
                bg-red-600 hover:bg-red-700 text-white
            @endif">

            @if($type == 'ending-soon')
            AMBIL SEKARANG
            @elseif($type == 'limited-slots')
            BOOKING SEKARANG
            @else
            LIHAT DETAIL
            @endif
        </a>
    </div>
</div>