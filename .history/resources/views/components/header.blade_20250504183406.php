<header class="bg-white shadow-md">
    <div class="container mx-auto px-4 py-3">
        <div class="flex justify-between items-center">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center">
                <picture>
                    <source srcset="{{ asset('/images/logo/logo.webp') }}" type="image/webp">
                    <source srcset="{{ asset('/images/logo/logo.png') }}" type="image/png">
                    <img src="{{ asset('/images/logo/logo.png') }}" alt="Hartono Motor Logo" class="h-12 mr-3"
                        width="85" height="48" style="object-fit: contain;">
                </picture>
                <span class="text-2xl font-bold text-red-600">HARTONO MOTOR</span>
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex space-x-8">
                <a href="{{ route('home') }}"
                    class="text-gray-900 hover:text-red-600 font-medium transition-colors">Beranda</a>
                <a href="{{ route('services') }}"
                    class="text-gray-900 hover:text-red-600 font-medium transition-colors">Layanan</a>
                <a href="{{ route('spare-parts') }}"
                    class="text-gray-900 hover:text-red-600 font-medium transition-colors">Sparepart</a>
                <a href="{{ route('promos') }}"
                    class="text-gray-900 hover:text-red-600 font-medium transition-colors relative group">
                    Promo
                    <span
                        class="absolute -top-2 -right-2 bg-red-600 text-white text-xs px-1.5 py-0.5 rounded-full animate-pulse">HOT</span>
                </a>
                <a href="{{ route('booking') }}"
                    class="text-gray-900 hover:text-red-600 font-medium transition-colors">Booking</a>
                <a href="{{ route('gallery') }}"
                    class="text-gray-900 hover:text-red-600 font-medium transition-colors">Galeri</a>
                <a href="{{ route('blog.index') }}"
                    class="text-gray-900 hover:text-red-600 font-medium transition-colors">Blog</a>
                <a href="{{ route('about') }}"
                    class="text-gray-900 hover:text-red-600 font-medium transition-colors">Tentang Kami</a>
                <a href="{{ route('contact') }}"
                    class="text-gray-900 hover:text-red-600 font-medium transition-colors">Kontak</a>
            </nav>

            <!-- Mobile Menu Button -->
            <button id="mobile-menu-button" aria-label="Menu Navigasi" aria-expanded="false" aria-controls="mobile-menu"
                class="md:hidden text-gray-900 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <!-- Mobile Navigation -->
        <div id="mobile-menu" class="md:hidden hidden mt-4 pb-4">
            <div class="flex flex-col space-y-3">
                <a href="{{ route('home') }}"
                    class="text-gray-900 hover:text-red-600 font-medium transition-colors">Beranda</a>
                <a href="{{ route('services') }}"
                    class="text-gray-900 hover:text-red-600 font-medium transition-colors">Layanan</a>
                <a href="{{ route('spare-parts') }}"
                    class="text-gray-900 hover:text-red-600 font-medium transition-colors">Sparepart</a>
                <a href="{{ route('promos') }}"
                    class="text-gray-900 hover:text-red-600 font-medium transition-colors relative inline-block">
                    Promo
                    <span class="ml-2 bg-red-600 text-white text-xs px-1.5 py-0.5 rounded-full animate-pulse">HOT</span>
                </a>
                <a href="{{ route('booking') }}"
                    class="text-gray-900 hover:text-red-600 font-medium transition-colors">Booking</a>
                <a href="{{ route('gallery') }}"
                    class="text-gray-900 hover:text-red-600 font-medium transition-colors">Galeri</a>
                <a href="{{ route('blog.index') }}"
                    class="text-gray-900 hover:text-red-600 font-medium transition-colors">Blog</a>
                <a href="{{ route('about') }}"
                    class="text-gray-900 hover:text-red-600 font-medium transition-colors">Tentang Kami</a>
                <a href="{{ route('contact') }}"
                    class="text-gray-900 hover:text-red-600 font-medium transition-colors">Kontak</a>
            </div>
        </div>
    </div>
</header>

<script>
    // Mobile menu toggle
    document.addEventListener('DOMContentLoaded', function () {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuButton.addEventListener('click', function () {
            const isExpanded = mobileMenu.classList.toggle('hidden') === false;
            mobileMenuButton.setAttribute('aria-expanded', isExpanded);
        });
    });
</script>