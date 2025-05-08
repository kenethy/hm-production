@extends('layouts.main')

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
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                                </path>
                            </svg>
                            Home
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <a href="{{ route('promos') }}"
                                class="text-gray-400 hover:text-white ml-1 md:ml-2">Promo</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-300 ml-1 md:ml-2">{{ Str::limit($promo->title, 30) }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <!-- Promo Detail -->
        <div class="bg-gray-900 rounded-lg overflow-hidden shadow-xl mb-12 reveal">
            <div class="md:flex">
                <div class="md:w-1/2 reveal-left">
                    @if($promo->image_path)
                    <img src="{{ asset('storage/' . $promo->image_path) }}" alt="{{ $promo->title }}"
                        class="w-full h-full object-cover img-zoom">
                    @else
                    <div class="w-full h-64 md:h-full bg-gray-800 flex items-center justify-center">
                        <span class="text-gray-500">No Image</span>
                    </div>
                    @endif
                </div>
                <div class="md:w-1/2 p-6 md:p-8 reveal-right">
                    <div class="flex flex-wrap gap-2 mb-4 animate-fade-in">
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

                    <h1 class="text-3xl md:text-4xl font-bold mb-4 stagger-text">{{ $promo->title }}</h1>

                    @if($promo->original_price)
                    <div class="mb-6">
                        <div class="flex items-center gap-4 mb-2">
                            <span class="text-gray-400 line-through text-xl">Rp {{ number_format($promo->original_price,
                                0, ',', '.') }}</span>
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
                        <div
                            class="bg-gray-800 border border-gray-700 rounded-lg p-3 flex justify-between items-center">
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
                            <div class="bg-red-600 h-2.5 rounded-full"
                                style="width: {{ min(100, max(10, ($promo->remaining_slots / 20) * 100)) }}%"></div>
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

                    <div class="flex flex-col sm:flex-row gap-4 animate-slide-up delay-300">
                        <a href="{{ route('booking') }}"
                            class="bg-red-600 hover:bg-red-700 text-white text-center font-bold py-3 px-6 rounded-full transition duration-300 flex-1 btn-animate animate-pulse-subtle">
                            BOOKING SEKARANG
                        </a>
                        <a href="https://wa.me/6281235202581?text=Halo%20Hartono%20Motor%2C%20saya%20tertarik%20dengan%20promo%20{{ urlencode($promo->title) }}."
                            target="_blank"
                            class="bg-green-500 hover:bg-green-600 text-white text-center font-bold py-3 px-6 rounded-full transition duration-300 flex-1 btn-animate">
                            TANYA VIA WHATSAPP
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Countdown Timer -->
        @if($promo->is_ending_soon)
        <x-promos.countdown :endDate="$promo->end_date->format('Y-m-d H:i:s')" />
        @endif

        <!-- Related Promos -->
        <x-promos.section title="PROMO LAINNYA" :promos="$relatedPromos" type="default" columns="4" />

        <!-- CTA Section -->
        <x-promos.cta />
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Copy promo code to clipboard
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function () {
            const button = document.getElementById('copyButton');
            button.textContent = 'Tersalin!';
            button.classList.add('bg-green-600');

            setTimeout(function () {
                button.textContent = 'Salin';
                button.classList.remove('bg-green-600');
            }, 2000);
        });
    }

    // Add some animation for FOMO effect
    document.addEventListener('DOMContentLoaded', function () {
        const animateElements = document.querySelectorAll('.animate-pulse');
        setInterval(() => {
            animateElements.forEach(el => {
                el.classList.toggle('scale-105');
            });
        }, 2000);
    });
</script>
@endsection