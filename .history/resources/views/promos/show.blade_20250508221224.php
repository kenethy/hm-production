@extends('layouts.app')

@section('title', $promo->title . ' - Hartono Motor')

@section('content')
<div class="bg-black text-white py-10">
    <div class="container mx-auto px-4">
        <!-- Breadcrumb -->
        <div class="mb-8">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('home') }}" class="text-gray-400 hover:text-white">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                            </svg>
                            Home
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <a href="{{ route('promos') }}" class="text-gray-400 hover:text-white ml-1 md:ml-2">Promo</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-300 ml-1 md:ml-2">{{ Str::limit($promo->title, 30) }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <!-- Promo Detail -->
        <div class="bg-gray-900 rounded-lg overflow-hidden shadow-xl mb-12">
            <div class="md:flex">
                <div class="md:w-1/2">
                    @if($promo->image_path)
                        <img src="{{ asset('storage/' . $promo->image_path) }}" alt="{{ $promo->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-64 md:h-full bg-gray-800 flex items-center justify-center">
                            <span class="text-gray-500">No Image</span>
                        </div>
                    @endif
                </div>
                <div class="md:w-1/2 p-6 md:p-8">
                    <div class="flex flex-wrap gap-2 mb-4">
                        @if($promo->is_featured)
                            <span class="bg-red-600 text-white px-3 py-1 rounded-full text-sm font-bold">
                                PROMO UNGGULAN
                            </span>
                        @endif
                        
                        @if($promo->is_ending_soon)
                            <span class="bg-yellow-500 text-black px-3 py-1 rounded-full text-sm font-bold animate-pulse">
                                BERAKHIR {{ $promo->remaining_time }}
                            </span>
                        @endif
                        
                        @if($promo->has_limited_slots)
                            <span class="bg-white text-red-600 px-3 py-1 rounded-full text-sm font-bold">
                                SISA {{ $promo->remaining_slots }} SLOT
                            </span>
                        @endif
                    </div>
                    
                    <h1 class="text-3xl md:text-4xl font-bold mb-4">{{ $promo->title }}</h1>
                    
                    @if($promo->original_price)
                    <div class="mb-6">
                        <div class="flex items-center gap-4 mb-2">
                            <span class="text-gray-400 line-through text-xl">Rp {{ number_format($promo->original_price, 0, ',', '.') }}</span>
                            <span class="bg-red-600 text-white px-3 py-1 rounded-full">
                                {{ $promo->discount_percentage }}% OFF
                            </span>
                        </div>
                        <div class="text-white font-bold text-4xl">
                            Rp {{ number_format($promo->promo_price, 0, ',', '.') }}
                        </div>
                    </div>
                    @endif
                    
                    <div class="prose prose-lg prose-invert mb-6">
                        <p>{{ $promo->description }}</p>
                    </div>
                    
                    @if($promo->promo_code)
                    <div class="mb-6">
                        <p class="text-gray-400 mb-2">Gunakan kode promo:</p>
                        <div class="bg-gray-800 border border-gray-700 rounded-lg p-3 flex justify-between items-center">
                            <span class="font-mono text-xl font-bold text-white">{{ $promo->promo_code }}</span>
                            <button id="copyButton" class="bg-gray-700 hover:bg-gray-600 text-white px-3 py-1 rounded" 
                                    onclick="copyToClipboard('{{ $promo->promo_code }}')">
                                Salin
                            </button>
                        </div>
                    </div>
                    @endif
                    
                    @if($promo->has_limited_slots)
                    <div class="mb-6">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-400">Sisa Slot:</span>
                            <span class="text-white font-bold">{{ $promo->remaining_slots }} tersisa</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-2.5">
                            <div class="bg-red-600 h-2.5 rounded-full" style="width: {{ min(100, max(10, ($promo->remaining_slots / 20) * 100)) }}%"></div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="mb-6">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-400">Periode Promo:</span>
                            <span class="text-white">
                                {{ $promo->start_date->format('d M Y') }} - {{ $promo->end_date->format('d M Y') }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('booking') }}" class="bg-red-600 hover:bg-red-700 text-white text-center font-bold py-3 px-6 rounded-full transition duration-300 flex-1">
                            BOOKING SEKARANG
                        </a>
                        <a href="https://wa.me/6281234567890?text=Halo%20Hartono%20Motor%2C%20saya%20tertarik%20dengan%20promo%20{{ urlencode($promo->title) }}." 
                           target="_blank" 
                           class="bg-green-500 hover:bg-green-600 text-white text-center font-bold py-3 px-6 rounded-full transition duration-300 flex-1">
                            TANYA VIA WHATSAPP
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Countdown Timer -->
        @if($promo->is_ending_soon)
        <div class="bg-yellow-500 text-black p-6 rounded-lg mb-12">
            <div class="text-center">
                <h3 class="text-2xl font-bold mb-4">PROMO INI AKAN SEGERA BERAKHIR!</h3>
                <div class="flex justify-center gap-4" id="countdown">
                    <div class="bg-white rounded-lg p-3 w-20 text-center">
                        <div class="text-3xl font-bold" id="days">00</div>
                        <div class="text-sm">Hari</div>
                    </div>
                    <div class="bg-white rounded-lg p-3 w-20 text-center">
                        <div class="text-3xl font-bold" id="hours">00</div>
                        <div class="text-sm">Jam</div>
                    </div>
                    <div class="bg-white rounded-lg p-3 w-20 text-center">
                        <div class="text-3xl font-bold" id="minutes">00</div>
                        <div class="text-sm">Menit</div>
                    </div>
                    <div class="bg-white rounded-lg p-3 w-20 text-center">
                        <div class="text-3xl font-bold" id="seconds">00</div>
                        <div class="text-sm">Detik</div>
                    </div>
                </div>
                <p class="mt-4 text-lg">Jangan sampai kehabisan! Ambil penawaran ini sekarang juga.</p>
            </div>
        </div>
        @endif

        <!-- Related Promos -->
        @if($relatedPromos->count() > 0)
        <div class="mb-12">
            <h2 class="text-3xl font-bold mb-8">PROMO LAINNYA</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedPromos as $relatedPromo)
                <div class="bg-gray-900 rounded-lg overflow-hidden shadow-lg">
                    <div class="relative">
                        @if($relatedPromo->image_path)
                            <img src="{{ asset('storage/' . $relatedPromo->image_path) }}" alt="{{ $relatedPromo->title }}" class="w-full h-40 object-cover">
                        @else
                            <div class="w-full h-40 bg-gray-800 flex items-center justify-center">
                                <span class="text-gray-500">No Image</span>
                            </div>
                        @endif
                        
                        @if($relatedPromo->is_ending_soon)
                        <div class="absolute bottom-0 left-0 bg-yellow-500 text-black px-3 py-1 m-2 rounded-full text-sm font-bold">
                            Berakhir {{ $relatedPromo->remaining_time }}
                        </div>
                        @endif
                    </div>
                    
                    <div class="p-4">
                        <h3 class="text-lg font-bold mb-2">{{ $relatedPromo->title }}</h3>
                        
                        @if($relatedPromo->original_price)
                        <div class="flex justify-between items-center mb-3">
                            <div>
                                <span class="text-gray-500 line-through text-sm">Rp {{ number_format($relatedPromo->original_price, 0, ',', '.') }}</span>
                                <span class="text-white font-bold block">Rp {{ number_format($relatedPromo->promo_price, 0, ',', '.') }}</span>
                            </div>
                            <div class="bg-red-600 text-white px-2 py-1 rounded-full text-sm">
                                {{ $relatedPromo->discount_percentage }}% OFF
                            </div>
                        </div>
                        @endif
                        
                        <a href="{{ route('promos.show', $relatedPromo->id) }}" class="block w-full bg-red-600 hover:bg-red-700 text-white text-center font-bold py-2 px-4 rounded-full transition duration-300 text-sm">
                            LIHAT DETAIL
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- CTA Section -->
        <div class="bg-gradient-to-r from-red-600 to-red-900 rounded-lg p-8 md:p-12 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">JANGAN LEWATKAN KESEMPATAN INI!</h2>
            <p class="text-xl mb-8">Dapatkan layanan terbaik untuk kendaraan Anda dengan harga spesial.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('booking') }}" class="bg-white text-red-600 hover:bg-gray-200 font-bold py-3 px-8 rounded-full inline-flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                    </svg>
                    BOOKING SEKARANG
                </a>
                <a href="https://wa.me/6281235202581" target="_blank" class="bg-green-500 text-white hover:bg-green-600 font-bold py-3 px-8 rounded-full inline-flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
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
    // Copy promo code to clipboard
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            const button = document.getElementById('copyButton');
            button.textContent = 'Tersalin!';
            button.classList.add('bg-green-600');
            
            setTimeout(function() {
                button.textContent = 'Salin';
                button.classList.remove('bg-green-600');
            }, 2000);
        });
    }
    
    // Countdown timer
    @if($promo->is_ending_soon)
    document.addEventListener('DOMContentLoaded', function() {
        const endDate = new Date("{{ $promo->end_date->format('Y-m-d H:i:s') }}").getTime();
        
        const countdown = setInterval(function() {
            const now = new Date().getTime();
            const distance = endDate - now;
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            document.getElementById("days").innerHTML = days.toString().padStart(2, '0');
            document.getElementById("hours").innerHTML = hours.toString().padStart(2, '0');
            document.getElementById("minutes").innerHTML = minutes.toString().padStart(2, '0');
            document.getElementById("seconds").innerHTML = seconds.toString().padStart(2, '0');
            
            if (distance < 0) {
                clearInterval(countdown);
                document.getElementById("countdown").innerHTML = "PROMO TELAH BERAKHIR";
            }
        }, 1000);
        
        // Add some animation for FOMO effect
        const animateElements = document.querySelectorAll('.animate-pulse');
        setInterval(() => {
            animateElements.forEach(el => {
                el.classList.toggle('scale-105');
            });
        }, 2000);
    });
    @endif
</script>
@endsection
