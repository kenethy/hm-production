@extends('layouts.main')

@section('title', 'Promo Spesial - Hartono Motor')

@section('content')

<div class="bg-black text-white py-10">
    <div class="container mx-auto px-4">
        <!-- Hero Section -->
        <x-promos.hero />

        <!-- Featured Promos -->
        <x-promos.section title="PROMO UNGGULAN" :promos="$featuredPromos" type="featured" columns="3"
            badge="Jangan Sampai Kehabisan!" badgeColor="red" />

        <!-- Ending Soon Promos -->
        <x-promos.section title="SEGERA BERAKHIR" :promos="$endingSoonPromos" type="ending-soon" columns="4"
            badge="Jangan Sampai Menyesal!" badgeColor="yellow" id="ending-soon" />

        <!-- Limited Slots Promos -->
        <x-promos.section title="SLOT TERBATAS" :promos="$limitedSlotPromos" type="limited-slots" columns="4"
            badge="Cepat Sebelum Habis!" badgeColor="white" id="limited-slots" />

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
    document.addEventListener('DOMContentLoaded', function () {
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