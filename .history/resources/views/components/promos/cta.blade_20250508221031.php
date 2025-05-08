@props([
    'title' => 'JANGAN LEWATKAN KESEMPATAN INI!',
    'subtitle' => 'Dapatkan layanan terbaik untuk kendaraan Anda dengan harga spesial.',
])

<div class="bg-gradient-to-r from-red-600 to-red-900 rounded-lg p-8 md:p-12 text-center">
    <h2 class="text-3xl md:text-4xl font-bold mb-4">{{ $title }}</h2>
    <p class="text-xl mb-8">{{ $subtitle }}</p>
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
