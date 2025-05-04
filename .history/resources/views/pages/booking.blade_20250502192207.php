@extends('layouts.main')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gray-900 text-white py-20">
    <div class="absolute inset-0 overflow-hidden">
        <img style="object-fit: cover; object-position: top; height: 300px;" src="{{ asset('images/hero-bg2.png') }}" alt="Booking Servis Hartono Motor"
            class="w-full h-full object-cover opacity-40">
    </div>
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-3xl">
            <h1 class="text-4xl md:text-5xl font-bold mb-4 animate-fade-in">Booking Servis Online</h1>
            <p class="text-xl animate-slide-up delay-200">Buat janji servis secara online untuk pengalaman servis yang
                lebih nyaman dan efisien.
            </p>
        </div>
    </div>
</section>

<!-- Booking Form Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6"
                role="alert">
                <strong class="font-bold">Berhasil!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif

            <div class="bg-gray-50 rounded-lg shadow-md p-8 reveal">
                <h2 class="text-2xl font-bold mb-6">Form Booking Servis</h2>

                <form action="{{ route('booking.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-gray-700 font-medium mb-2">Nama Lengkap</label>
                            <input type="text" name="name" id="name"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                required>
                            @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-gray-700 font-medium mb-2">Nomor Telepon</label>
                            <input type="tel" name="phone" id="phone"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                required>
                            @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Car Model -->
                        <div>
                            <label for="car_model" class="block text-gray-700 font-medium mb-2">Model Mobil</label>
                            <input type="text" name="car_model" id="car_model"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                required>
                            @error('car_model')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Service Type -->
                        <div>
                            <label for="service_type" class="block text-gray-700 font-medium mb-2">Jenis Servis</label>
                            <select name="service_type" id="service_type"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                required>
                                <option value="">Pilih Jenis Servis</option>
                                <option value="Servis Berkala">Servis Berkala</option>
                                <option value="Tune Up Mesin">Tune Up Mesin</option>
                                <option value="Servis AC">Servis AC</option>
                                <option value="Ganti Oli">Ganti Oli</option>
                                <option value="Perbaikan Rem">Perbaikan Rem</option>
                                <option value="Balancing & Spooring">Balancing & Spooring</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                            @error('service_type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date -->
                        <div>
                            <label for="date" class="block text-gray-700 font-medium mb-2">Tanggal Servis</label>
                            <input type="date" name="date" id="date"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                required>
                            @error('date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Time -->
                        <div>
                            <label for="time" class="block text-gray-700 font-medium mb-2">Waktu Servis</label>
                            <select name="time" id="time"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                required>
                                <option value="">Pilih Waktu</option>
                                <option value="08:00">08:00</option>
                                <option value="09:00">09:00</option>
                                <option value="10:00">10:00</option>
                                <option value="11:00">11:00</option>
                                <option value="13:00">13:00</option>
                                <option value="14:00">14:00</option>
                                <option value="15:00">15:00</option>
                                <option value="16:00">16:00</option>
                            </select>
                            @error('time')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Message -->
                    <div class="mb-6">
                        <label for="message" class="block text-gray-700 font-medium mb-2">Pesan Tambahan
                            (Opsional)</label>
                        <textarea name="message" id="message" rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"></textarea>
                        @error('message')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="text-center">
                        <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-md transition-colors btn-animate">Kirim
                            Booking</button>
                    </div>
                </form>
            </div>

            <div class="mt-12 text-center reveal">
                <h3 class="text-xl font-bold mb-4">Butuh Bantuan?</h3>
                <p class="text-gray-600 mb-4">Jika Anda memiliki pertanyaan atau membutuhkan bantuan dalam proses
                    booking, silakan hubungi kami.</p>
                <div class="flex justify-center space-x-4">
                    <a href="tel:+62318912345"
                        class="flex items-center text-gray-700 hover:text-red-600 transition-colors hover-scale">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        (031) 8912345
                    </a>
                    <a href="https://wa.me/6281234567890" target="_blank"
                        class="flex items-center text-gray-700 hover:text-red-600 transition-colors hover-scale">
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
</section>
@endsection