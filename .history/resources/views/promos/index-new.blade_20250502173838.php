@extends('layouts.main')

@section('title', 'Promo Spesial - Hartono Motor')

@section('content')
<!-- Debugging Section (Only visible in development) -->
@if(config('app.debug'))
<div class="bg-gray-800 text-white p-4 mb-4">
    <h2 class="text-xl font-bold mb-2">Debug Info</h2>
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-gray-700 p-3 rounded">
            <h3 class="font-bold">Total Promos</h3>
            <p>{{ $debug['total_promos'] }}</p>
        </div>
        <div class="bg-gray-700 p-3 rounded">
            <h3 class="font-bold">Featured</h3>
            <p>{{ $debug['featured_count'] }}</p>
        </div>
        <div class="bg-gray-700 p-3 rounded">
            <h3 class="font-bold">Active</h3>
            <p>{{ $debug['active_count'] }}</p>
        </div>
        <div class="bg-gray-700 p-3 rounded">
            <h3 class="font-bold">Ending Soon</h3>
            <p>{{ $debug['ending_soon_count'] }}</p>
        </div>
        <div class="bg-gray-700 p-3 rounded">
            <h3 class="font-bold">Limited Slots</h3>
            <p>{{ $debug['limited_slots_count'] }}</p>
        </div>
    </div>
    
    <div class="mt-4">
        <h3 class="font-bold mb-2">All Promos in Database:</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($allPromos as $promo)
            <div class="bg-gray-700 p-3 rounded text-sm">
                <p><strong>ID:</strong> {{ $promo->id }}</p>
                <p><strong>Title:</strong> {{ $promo->title }}</p>
                <p><strong>Featured:</strong> {{ $promo->is_featured ? 'Yes' : 'No' }}</p>
                <p><strong>Active:</strong> {{ $promo->is_active ? 'Yes' : 'No' }}</p>
                <p><strong>Start:</strong> {{ $promo->start_date->format('Y-m-d') }}</p>
                <p><strong>End:</strong> {{ $promo->end_date->format('Y-m-d') }}</p>
                <p><strong>Slots:</strong> {{ $promo->remaining_slots ?? 'Unlimited' }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<div class="bg-black text-white py-10">
    <div class="container mx-auto px-4">
        <!-- Hero Section -->
        <x-promos.hero />

        <!-- Featured Promos -->
        <x-promos.section 
            title="PROMO UNGGULAN" 
            :promos="$featuredPromos" 
            type="featured" 
            columns="3" 
            badge="Jangan Sampai Kehabisan!" 
            badgeColor="red" />

        <!-- Ending Soon Promos -->
        <x-promos.section 
            title="SEGERA BERAKHIR" 
            :promos="$endingSoonPromos" 
            type="ending-soon" 
            columns="4" 
            badge="Jangan Sampai Menyesal!" 
            badgeColor="yellow" 
            id="ending-soon" />

        <!-- Limited Slots Promos -->
        <x-promos.section 
            title="SLOT TERBATAS" 
            :promos="$limitedSlotPromos" 
            type="limited-slots" 
            columns="4" 
            badge="Cepat Sebelum Habis!" 
            badgeColor="white" 
            id="limited-slots" />

        <!-- All Promos -->
        <div class="mb-16">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold">SEMUA PROMO</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($promos as $promo)
                    <x-promos.card :promo="$promo" type="default" />
                @endforeach
            </div>
            
            <div class="mt-8">
                {{ $promos->links() }}
            </div>
        </div>

        <!-- CTA Section -->
        <x-promos.cta />
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Add countdown timer for ending soon promos
    document.addEventListener('DOMContentLoaded', function() {
        // Add some animation for FOMO effect
        const animateElements = document.querySelectorAll('.animate-pulse');
        setInterval(() => {
            animateElements.forEach(el => {
                el.classList.toggle('scale-105');
            });
        }, 2000);
    });
</script>
@endsection
