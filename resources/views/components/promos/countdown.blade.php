@props([
    'endDate',
    'title' => 'PROMO INI AKAN SEGERA BERAKHIR!',
    'message' => 'Jangan sampai kehabisan! Ambil penawaran ini sekarang juga.'
])

<div class="bg-yellow-500 text-black p-6 rounded-lg mb-12">
    <div class="text-center">
        <h3 class="text-2xl font-bold mb-4">{{ $title }}</h3>
        <div class="flex justify-center gap-4" id="countdown">
            <div class="bg-white rounded-lg p-3 w-20 text-center">
                <div class="text-3xl font-bold" id="days">00</div>
                <div class="text-sm">Hari</div>
            </div>
            <div class="bg-white rounded-lg p-3 w-20 text-center">
                <div class="text-3xl font-bold" id="hours">00</div>
                <div class="text-sm">Jam</div>
            </div>
            <div class="bg-white rounded-lg p-3 w-20 text-center">
                <div class="text-3xl font-bold" id="minutes">00</div>
                <div class="text-sm">Menit</div>
            </div>
            <div class="bg-white rounded-lg p-3 w-20 text-center">
                <div class="text-3xl font-bold" id="seconds">00</div>
                <div class="text-sm">Detik</div>
            </div>
        </div>
        <p class="mt-4 text-lg">{{ $message }}</p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const endDate = new Date("{{ $endDate }}").getTime();
        
        const countdown = setInterval(function() {
            const now = new Date().getTime();
            const distance = endDate - now;
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            document.getElementById("days").innerHTML = days.toString().padStart(2, '0');
            document.getElementById("hours").innerHTML = hours.toString().padStart(2, '0');
            document.getElementById("minutes").innerHTML = minutes.toString().padStart(2, '0');
            document.getElementById("seconds").innerHTML = seconds.toString().padStart(2, '0');
            
            if (distance < 0) {
                clearInterval(countdown);
                document.getElementById("countdown").innerHTML = "PROMO TELAH BERAKHIR";
            }
        }, 1000);
    });
</script>
