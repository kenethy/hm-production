@props([
    'title',
    'promos',
    'type' => 'default', // default, featured, ending-soon, limited-slots
    'columns' => 3,
    'badge' => null,
    'badgeColor' => 'red',
    'id' => null,
])

@if($promos->count() > 0)
<div @if($id) id="{{ $id }}" @endif class="mb-16">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-3xl font-bold">{{ $title }}</h2>
        
        @if($badge)
        <div class="hidden md:block">
            <span class="inline-block 
                @if($badgeColor == 'red') bg-red-600 text-white 
                @elseif($badgeColor == 'yellow') bg-yellow-500 text-black
                @elseif($badgeColor == 'white') bg-white text-red-600
                @endif
                px-4 py-2 rounded-full animate-pulse">
                {{ $badge }}
            </span>
        </div>
        @endif
    </div>
    
    <div class="grid grid-cols-1 
        @if($columns == 2) md:grid-cols-2 
        @elseif($columns == 3) md:grid-cols-3 
        @elseif($columns == 4) md:grid-cols-2 lg:grid-cols-4 
        @endif
        gap-6">
        
        @foreach($promos as $promo)
            <x-promos.card :promo="$promo" :type="$type" />
        @endforeach
    </div>
</div>
@endif
