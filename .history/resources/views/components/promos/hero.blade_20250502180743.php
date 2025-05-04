@props([
'title' => 'PROMO SPESIAL HARTONO MOTOR',
'subtitle' => 'Dapatkan penawaran terbaik untuk perawatan kendaraan Anda!',
'buttons' => true,
])

<div class="relative overflow-hidden rounded-lg mb-12">
    <div class="absolute inset-0 bg-gradient-to-r from-red-600 to-red-900 opacity-90"></div>
    <div class="relative z-10 py-16 px-8 md:py-24 md:px-12">
        <h1 class="text-4xl md:text-5xl font-bold mb-4 animate-fade-in">{{ $title }}</h1>
        <p class="text-xl md:text-2xl mb-8 animate-slide-up delay-200">{{ $subtitle }}</p>

        @if($buttons)
        <div class="flex flex-col sm:flex-row gap-4 animate-slide-up delay-400">
            <a href="#limited-slots"
                class="bg-white text-red-600 hover:bg-gray-200 font-bold py-3 px-6 rounded-full inline-flex items-center btn-animate">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                        clip-rule="evenodd" />
                </svg>
                Slot Terbatas
            </a>
            <a href="#ending-soon"
                class="bg-red-600 text-white hover:bg-red-700 font-bold py-3 px-6 rounded-full inline-flex items-center btn-animate animate-pulse-subtle">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                        clip-rule="evenodd" />
                </svg>
                Segera Berakhir
            </a>
        </div>
        @endif
    </div>
</div>