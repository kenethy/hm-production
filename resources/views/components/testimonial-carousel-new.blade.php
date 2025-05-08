<div class="testimonial-section" aria-label="Testimonial Pelanggan">
    <div class="testimonial-container">
        <div class="testimonial-carousel" id="testimonialCarousel">
            <div class="testimonial-track">
                <!-- Testimonial 1 -->
                <div class="testimonial-slide" data-index="0">
                    <div class="testimonial-card">
                        <div class="testimonial-customer">
                            <img src="{{ asset('images/testimonial-1.jpg') }}" alt="Foto Budi Santoso" class="testimonial-avatar">
                            <div class="testimonial-info">
                                <h4>Budi Santoso</h4>
                                <p>Pelanggan Tetap</p>
                            </div>
                        </div>
                        <div class="testimonial-rating" aria-label="Rating 5 dari 5 bintang">
                            <span class="sr-only">Rating 5 dari 5 bintang</span>
                            @for ($i = 0; $i < 5; $i++)
                                <svg class="star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />
                                </svg>
                            @endfor
                        </div>
                        <div class="testimonial-content">
                            <p>"Sudah lebih dari 5 tahun saya selalu servis mobil di Hartono Motor. Pelayanannya cepat, mekaniknya profesional, dan hasil servisnya selalu memuaskan. Recommended!"</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="testimonial-slide" data-index="1">
                    <div class="testimonial-card">
                        <div class="testimonial-customer">
                            <img src="{{ asset('images/testimonial-2.jpg') }}" alt="Foto Siti Rahayu" class="testimonial-avatar">
                            <div class="testimonial-info">
                                <h4>Siti Rahayu</h4>
                                <p>Pelanggan Baru</p>
                            </div>
                        </div>
                        <div class="testimonial-rating" aria-label="Rating 5 dari 5 bintang">
                            <span class="sr-only">Rating 5 dari 5 bintang</span>
                            @for ($i = 0; $i < 5; $i++)
                                <svg class="star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />
                                </svg>
                            @endfor
                        </div>
                        <div class="testimonial-content">
                            <p>"Pertama kali servis di sini dan sangat puas dengan hasilnya. AC mobil saya yang tadinya tidak dingin sekarang sudah normal kembali. Harga juga terjangkau dan transparan."</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="testimonial-slide" data-index="2">
                    <div class="testimonial-card">
                        <div class="testimonial-customer">
                            <img src="{{ asset('images/testimonial-3.jpg') }}" alt="Foto Ahmad Hidayat" class="testimonial-avatar">
                            <div class="testimonial-info">
                                <h4>Ahmad Hidayat</h4>
                                <p>Pelanggan Tetap</p>
                            </div>
                        </div>
                        <div class="testimonial-rating" aria-label="Rating 4 dari 5 bintang">
                            <span class="sr-only">Rating 4 dari 5 bintang</span>
                            @for ($i = 0; $i < 4; $i++)
                                <svg class="star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />
                                </svg>
                            @endfor
                            <svg class="star star-empty" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="testimonial-content">
                            <p>"Yang saya suka dari Hartono Motor adalah ketersediaan sparepart yang lengkap. Tidak perlu menunggu lama untuk penggantian komponen. Mekaniknya juga ramah dan informatif."</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 4 -->
                <div class="testimonial-slide" data-index="3">
                    <div class="testimonial-card">
                        <div class="testimonial-customer">
                            <img src="{{ asset('images/pelanggan/Dew.png') }}" alt="Foto Dewi Lestari" class="testimonial-avatar">
                            <div class="testimonial-info">
                                <h4>Dewi Lestari</h4>
                                <p>Pelanggan Baru</p>
                            </div>
                        </div>
                        <div class="testimonial-rating" aria-label="Rating 5 dari 5 bintang">
                            <span class="sr-only">Rating 5 dari 5 bintang</span>
                            @for ($i = 0; $i < 5; $i++)
                                <svg class="star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />
                                </svg>
                            @endfor
                        </div>
                        <div class="testimonial-content">
                            <p>"Saya sangat senang dengan pelayanan di Hartono Motor. Mobil saya yang bermasalah dengan transmisi langsung ditangani dengan cepat dan profesional. Terima kasih!"</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 5 -->
                <div class="testimonial-slide" data-index="4">
                    <div class="testimonial-card">
                        <div class="testimonial-customer">
                            <img src="{{ asset('images/testimonial-2.jpg') }}" alt="Foto Rudi Hartono" class="testimonial-avatar">
                            <div class="testimonial-info">
                                <h4>Rudi Hartono</h4>
                                <p>Pelanggan Tetap</p>
                            </div>
                        </div>
                        <div class="testimonial-rating" aria-label="Rating 4 dari 5 bintang">
                            <span class="sr-only">Rating 4 dari 5 bintang</span>
                            @for ($i = 0; $i < 4; $i++)
                                <svg class="star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />
                                </svg>
                            @endfor
                            <svg class="star star-empty" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="testimonial-content">
                            <p>"Bengkel langganan saya sejak lama. Harga terjangkau, kualitas bagus, dan selalu memberikan saran perawatan yang tepat untuk mobil saya. Recommended!"</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation arrows -->
            <div class="testimonial-arrows">
                <button class="testimonial-arrow prev" aria-label="Testimonial sebelumnya">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </button>
                <button class="testimonial-arrow next" aria-label="Testimonial berikutnya">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Navigation dots -->
        <div class="testimonial-nav">
            <div class="testimonial-dots" id="testimonialDots">
                @for ($i = 0; $i < 5; $i++)
                    <button class="testimonial-dot @if($i === 0) active @endif" data-index="{{ $i }}" aria-label="Testimonial {{ $i + 1 }}"></button>
                @endfor
            </div>
        </div>
    </div>
</div>
