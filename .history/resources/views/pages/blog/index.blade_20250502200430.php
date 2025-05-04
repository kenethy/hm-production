@extends('layouts.main')

@section('content')
    <!-- Hero Section -->
    <section class="relative bg-gray-900 text-white py-20">
        <div class="absolute inset-0 overflow-hidden">
            <img src="{{ asset('images/blog-bg.jpg') }}" alt="Blog Hartono Motor" class="w-full h-full object-cover opacity-40" style="object-position: center 30%;">
        </div>
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-3xl">
                <h1 class="text-4xl md:text-5xl font-bold mb-4 animate-fade-in">Blog</h1>
                <p class="text-xl animate-slide-up delay-200">Tips dan informasi seputar perawatan mobil untuk Anda.</p>
            </div>
        </div>
    </section>

    <!-- Featured Posts (Only on main blog page) -->
    @if($activeCategory === null && $activeTag === null && $search === null && $featuredPosts->count() > 0)
    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl font-bold mb-8 reveal">Artikel Unggulan</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($featuredPosts as $post)
                <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow hover-lift reveal-up">
                    <a href="{{ route('blog.show', $post->slug) }}">
                        <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-48 object-cover">
                    </a>
                    <div class="p-6">
                        <div class="flex items-center text-gray-500 text-sm mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>{{ $post->formatted_published_date }}</span>
                            <span class="mx-2">•</span>
                            <a href="{{ route('blog.category', $post->category->slug) }}" class="hover:text-red-600 transition-colors">
                                {{ $post->category->name }}
                            </a>
                        </div>
                        <h3 class="text-xl font-bold mb-2">
                            <a href="{{ route('blog.show', $post->slug) }}" class="text-gray-900 hover:text-red-600 transition-colors">{{ $post->title }}</a>
                        </h3>
                        <p class="text-gray-600 mb-4">{{ $post->excerpt }}</p>
                        <a href="{{ route('blog.show', $post->slug) }}" class="inline-flex items-center text-red-600 font-medium hover:text-red-700 transition-colors">
                            Baca Selengkapnya
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Blog Content -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Blog Posts -->
                <div class="lg:col-span-2">
                    <!-- Filter Info -->
                    @if($activeCategory || $activeTag || $search)
                    <div class="mb-8 p-4 bg-gray-50 rounded-lg reveal">
                        <div class="flex items-center justify-between">
                            <div>
                                @if($activeCategory)
                                <p class="text-gray-700">
                                    Menampilkan artikel dalam kategori: <span class="font-semibold">{{ $categories->where('slug', $activeCategory)->first()->name }}</span>
                                </p>
                                @elseif($activeTag)
                                <p class="text-gray-700">
                                    Menampilkan artikel dengan tag: <span class="font-semibold">{{ $popularTags->where('slug', $activeTag)->first()->name }}</span>
                                </p>
                                @elseif($search)
                                <p class="text-gray-700">
                                    Hasil pencarian untuk: <span class="font-semibold">{{ $search }}</span>
                                </p>
                                @endif
                            </div>
                            <a href="{{ route('blog.index') }}" class="text-red-600 hover:text-red-700 font-medium">
                                Lihat Semua Artikel
                            </a>
                        </div>
                    </div>
                    @endif

                    @if($posts->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        @foreach($posts as $post)
                        <!-- Blog Post -->
                        <div class="bg-gray-50 rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow reveal-up">
                            <a href="{{ route('blog.show', $post->slug) }}">
                                <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-48 object-cover">
                            </a>
                            <div class="p-6">
                                <div class="flex items-center text-gray-500 text-sm mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>{{ $post->formatted_published_date }}</span>
                                    <span class="mx-2">•</span>
                                    <a href="{{ route('blog.category', $post->category->slug) }}" class="hover:text-red-600 transition-colors">
                                        {{ $post->category->name }}
                                    </a>
                                </div>
                                <h3 class="text-xl font-bold mb-2">
                                    <a href="{{ route('blog.show', $post->slug) }}" class="text-gray-900 hover:text-red-600 transition-colors">{{ $post->title }}</a>
                                </h3>
                                <p class="text-gray-600 mb-4">{{ $post->excerpt }}</p>
                                <a href="{{ route('blog.show', $post->slug) }}" class="inline-flex items-center text-red-600 font-medium hover:text-red-700 transition-colors">
                                    Baca Selengkapnya
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($posts->hasPages())
                    <div class="mt-10 reveal">
                        {{ $posts->links() }}
                    </div>
                    @endif
                    
                    @else
                    <div class="bg-gray-50 rounded-lg p-12 text-center shadow-md reveal">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="text-xl font-bold mb-2">Tidak Ada Artikel</h3>
                        <p class="text-gray-600 mb-6">Belum ada artikel yang tersedia untuk kriteria pencarian ini.</p>
                        <a href="{{ route('blog.index') }}" class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                            </svg>
                            Kembali ke Semua Artikel
                        </a>
                    </div>
                    @endif
                </div>
                
                <!-- Sidebar -->
                <div>
                    <!-- Search -->
                    <div class="bg-gray-50 rounded-lg p-6 shadow-md mb-8 reveal">
                        <h3 class="text-lg font-bold mb-4">Cari Artikel</h3>
                        <form action="{{ route('blog.index') }}" method="GET">
                            <div class="flex">
                                <input type="text" name="search" value="{{ $search }}" placeholder="Kata kunci..." class="flex-grow px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-r-md transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Categories -->
                    <div class="bg-gray-50 rounded-lg p-6 shadow-md mb-8 reveal">
                        <h3 class="text-lg font-bold mb-4">Kategori</h3>
                        <ul class="space-y-2">
                            @foreach($categories as $category)
                            <li>
                                <a href="{{ route('blog.category', $category->slug) }}" class="flex items-center text-gray-700 hover:text-red-600 transition-colors {{ $activeCategory === $category->slug ? 'text-red-600 font-medium' : '' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                    {{ $category->name }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    
                    <!-- Popular Tags -->
                    <div class="bg-gray-50 rounded-lg p-6 shadow-md mb-8 reveal">
                        <h3 class="text-lg font-bold mb-4">Tag Populer</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($popularTags as $tag)
                            <a href="{{ route('blog.tag', $tag->slug) }}" class="inline-block px-3 py-1 bg-gray-200 text-gray-800 rounded-full text-sm hover:bg-red-100 hover:text-red-600 transition-colors {{ $activeTag === $tag->slug ? 'bg-red-100 text-red-600 font-medium' : '' }}">
                                #{{ $tag->name }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- CTA -->
                    <div class="bg-red-600 rounded-lg p-6 shadow-md text-white reveal">
                        <h3 class="text-lg font-bold mb-4">Butuh Bantuan?</h3>
                        <p class="mb-6">Tim mekanik profesional kami siap membantu menjawab pertanyaan Anda seputar perawatan dan perbaikan mobil.</p>
                        <a href="{{ route('contact') }}" class="inline-block bg-white text-red-600 font-medium py-2 px-4 rounded-md hover:bg-gray-100 transition-colors">Hubungi Kami</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gray-100">
        <div class="container mx-auto px-4">
            <div class="bg-white rounded-lg shadow-md p-8 md:p-12 reveal">
                <div class="text-center max-w-3xl mx-auto">
                    <h2 class="text-3xl font-bold mb-4">Punya Pertanyaan Seputar Mobil?</h2>
                    <p class="text-gray-600 mb-8">Tim mekanik profesional kami siap membantu menjawab pertanyaan Anda seputar perawatan dan perbaikan mobil. Hubungi kami atau booking servis sekarang.</p>
                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                        <a href="{{ route('booking') }}" class="bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-md transition-colors btn-animate">Booking Servis</a>
                        <a href="{{ route('contact') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-900 font-medium py-3 px-6 rounded-md transition-colors btn-animate">Hubungi Kami</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
