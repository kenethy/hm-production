@extends('layouts.main')

@section('schema')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BlogPosting",
    "headline": "{{ $post->title }}",
    "image": "{{ asset('storage/' . $post->featured_image) }}",
    "datePublished": "{{ $post->published_at->toIso8601String() }}",
    "dateModified": "{{ $post->updated_at->toIso8601String() }}",
    "author": {
        "@type": "Person",
        "name": "{{ $post->author->name }}"
    },
    "publisher": {
        "@type": "Organization",
        "name": "Hartono Motor",
        "logo": {
            "@type": "ImageObject",
            "url": "{{ asset('images/logo.png') }}"
        }
    },
    "description": "{{ strip_tags($post->excerpt) }}",
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "{{ request()->url() }}"
    },
    "wordCount": "{{ str_word_count(strip_tags($post->content)) }}",
    "keywords": "{{ $post->tags->pluck('name')->join(', ') }}"
}
</script>
@endsection

@section('content')
<!-- Hero Section -->
<section class="relative bg-gray-900 text-white py-20">
    <div class="absolute inset-0 overflow-hidden">
        <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}"
            class="w-full h-full object-cover opacity-40" style="object-position: center 30%;" fetchpriority="high">
    </div>
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-3xl">
            <h1 class="text-4xl md:text-5xl font-bold mb-4 animate-fade-in">{{ $post->title }}</h1>
            <div class="flex items-center text-gray-300 text-sm animate-slide-up">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>{{ $post->formatted_published_date }}</span>
                <span class="mx-2">•</span>
                <a href="{{ route('blog.category', $post->category->slug) }}"
                    class="hover:text-white transition-colors">
                    {{ $post->category->name }}
                </a>
                <span class="mx-2">•</span>
                <span>{{ $post->reading_time }} membaca</span>
            </div>
        </div>
    </div>
</section>

<!-- Breadcrumbs -->
<x-breadcrumbs :breadcrumbs="$breadcrumbs" />

<!-- Blog Content -->
<section class="py-8 bg-white">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md overflow-hidden reveal">
                    <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}"
                        class="w-full h-96 object-cover" loading="lazy">
                    <div class="p-8">
                        <!-- Author Info -->
                        <div class="flex items-center mb-6 pb-6 border-b border-gray-200">
                            <div class="flex-shrink-0 mr-4">
                                <div
                                    class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center text-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <p class="font-medium">{{ $post->author->name }}</p>
                                <p class="text-gray-500 text-sm">Penulis</p>
                            </div>
                        </div>

                        <!-- Article Content -->
                        <div class="prose prose-lg max-w-none">
                            {!! $post->content !!}
                        </div>

                        <!-- Tags -->
                        @if($post->tags->count() > 0)
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="flex flex-wrap gap-2">
                                @foreach($post->tags as $tag)
                                <a href="{{ route('blog.tag', $tag->slug) }}"
                                    class="inline-block px-3 py-1 bg-gray-200 text-gray-800 rounded-full text-sm hover:bg-red-100 hover:text-red-600 transition-colors">
                                    #{{ $tag->name }}
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Share Buttons -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <h3 class="text-lg font-bold mb-4">Bagikan Artikel</h3>
                            <div class="flex flex-wrap gap-4">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                                    target="_blank"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z" />
                                    </svg>
                                    Facebook
                                </a>
                                <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(request()->url()) }}"
                                    target="_blank"
                                    class="bg-blue-400 hover:bg-blue-500 text-white px-4 py-2 rounded-md transition-colors flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                                    </svg>
                                    Twitter
                                </a>
                                <a href="https://wa.me/?text={{ urlencode($post->title . ' ' . request()->url()) }}"
                                    target="_blank"
                                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md transition-colors flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                                    </svg>
                                    WhatsApp
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div>
                <!-- Search -->
                <div class="bg-gray-50 rounded-lg p-6 shadow-md mb-8 reveal">
                    <h3 class="text-lg font-bold mb-4">Cari Artikel</h3>
                    <form action="{{ route('blog.index') }}" method="GET">
                        <div class="flex">
                            <input type="text" name="search" placeholder="Kata kunci..."
                                class="flex-grow px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <button type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-r-md transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Popular Tags -->
                <div class="bg-gray-50 rounded-lg p-6 shadow-md mb-8 reveal">
                    <h3 class="text-lg font-bold mb-4">Tag Populer</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($popularTags as $tag)
                        <a href="{{ route('blog.tag', $tag->slug) }}"
                            class="inline-block px-3 py-1 bg-gray-200 text-gray-800 rounded-full text-sm hover:bg-red-100 hover:text-red-600 transition-colors">
                            #{{ $tag->name }}
                        </a>
                        @endforeach
                    </div>
                </div>

                <!-- CTA -->
                <div class="bg-red-600 rounded-lg p-6 shadow-md text-white reveal">
                    <h3 class="text-lg font-bold mb-4">Butuh Bantuan?</h3>
                    <p class="mb-6">Tim mekanik profesional kami siap membantu menjawab pertanyaan Anda seputar
                        perawatan dan perbaikan mobil.</p>
                    <a href="{{ route('contact') }}"
                        class="inline-block bg-white text-red-600 font-medium py-2 px-4 rounded-md hover:bg-gray-100 transition-colors">Hubungi
                        Kami</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Posts -->
@if($relatedPosts->count() > 0)
<section class="py-16 bg-gray-100">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold mb-8 reveal">Artikel Terkait</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($relatedPosts as $relatedPost)
            <!-- Related Post -->
            <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow reveal-up">
                <a href="{{ route('blog.show', $relatedPost->slug) }}">
                    <img src="{{ asset('storage/' . $relatedPost->featured_image) }}" alt="{{ $relatedPost->title }}"
                        class="w-full h-48 object-cover">
                </a>
                <div class="p-6">
                    <div class="flex items-center text-gray-500 text-sm mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>{{ $relatedPost->formatted_published_date }}</span>
                    </div>
                    <h3 class="text-xl font-bold mb-2">
                        <a href="{{ route('blog.show', $relatedPost->slug) }}"
                            class="text-gray-900 hover:text-red-600 transition-colors">{{ $relatedPost->title }}</a>
                    </h3>
                    <p class="text-gray-600 mb-4">{{ $relatedPost->excerpt }}</p>
                    <a href="{{ route('blog.show', $relatedPost->slug) }}"
                        class="inline-flex items-center text-red-600 font-medium hover:text-red-700 transition-colors">
                        Baca Selengkapnya
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
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

<!-- Back to Blog Button -->
<section class="py-8 bg-white">
    <div class="container mx-auto px-4 text-center reveal">
        <a href="{{ route('blog.index') }}"
            class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-md transition-colors btn-animate">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18" />
            </svg>
            Kembali ke Blog
        </a>
    </div>
</section>
@endsection