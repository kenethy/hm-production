@extends('layouts.main')

@section('content')
    <!-- Gallery Detail Section -->
    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <!-- Breadcrumb -->
            <div class="mb-8">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('home') }}" class="text-gray-700 hover:text-red-600">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                </svg>
                                Home
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <a href="{{ route('gallery') }}" class="text-gray-700 hover:text-red-600 ml-1 md:ml-2">Galeri</a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <a href="{{ route('gallery', ['category' => $galleryItem->category->slug]) }}" class="text-gray-700 hover:text-red-600 ml-1 md:ml-2">{{ $galleryItem->category->name }}</a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-500 ml-1 md:ml-2">{{ $galleryItem->title }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Gallery Image -->
            <div class="bg-gray-100 rounded-lg overflow-hidden shadow-lg mb-8 reveal">
                <img src="{{ asset('storage/' . $galleryItem->image_path) }}" alt="{{ $galleryItem->title }}" class="w-full object-cover">
            </div>

            <!-- Gallery Info -->
            <div class="mb-12 reveal-up">
                <h1 class="text-3xl font-bold mb-4">{{ $galleryItem->title }}</h1>
                <div class="flex items-center mb-6">
                    <span class="bg-gray-200 text-gray-800 px-3 py-1 rounded-full text-sm font-medium">{{ $galleryItem->category->name }}</span>
                </div>
                @if($galleryItem->description)
                <div class="prose max-w-none">
                    <p>{{ $galleryItem->description }}</p>
                </div>
                @endif
            </div>

            <!-- Related Gallery Items -->
            @if($relatedItems->count() > 0)
            <div class="mb-12 reveal">
                <h2 class="text-2xl font-bold mb-6">Foto Terkait</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedItems as $item)
                    <div class="overflow-hidden rounded-lg shadow-md hover:shadow-lg transition-shadow hover-lift">
                        <a href="{{ route('gallery.show', $item->id) }}" class="block relative group">
                            <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}" class="w-full h-48 object-cover">
                            <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                <div class="text-white text-center p-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                    </svg>
                                    <h3 class="font-bold">{{ $item->title }}</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Back to Gallery Button -->
            <div class="text-center reveal">
                <a href="{{ route('gallery') }}" class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-md transition-colors btn-animate">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Kembali ke Galeri
                </a>
            </div>
        </div>
    </section>
@endsection
