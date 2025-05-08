/**
 * Modern Testimonial Carousel
 * A responsive, accessible testimonial carousel with smooth transitions
 */
document.addEventListener('DOMContentLoaded', function() {
    // Get carousel elements
    const carousel = document.getElementById('testimonialCarousel');
    if (!carousel) return;

    const slides = carousel.querySelectorAll('.testimonial-slide');
    const dots = document.querySelectorAll('.testimonial-dot');
    const prevButton = carousel.querySelector('.testimonial-arrow.prev');
    const nextButton = carousel.querySelector('.testimonial-arrow.next');
    
    // Set initial state
    let currentIndex = 0;
    let interval;
    const autoplayDelay = 5000; // 5 seconds between slides
    
    // Initialize the carousel
    function initCarousel() {
        // Show the first slide
        showSlide(currentIndex);
        
        // Start autoplay
        startAutoplay();
        
        // Add event listeners
        prevButton.addEventListener('click', showPrevSlide);
        nextButton.addEventListener('click', showNextSlide);
        
        // Add event listeners to dots
        dots.forEach(dot => {
            dot.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-index'));
                showSlide(index);
                resetAutoplay();
            });
        });
        
        // Pause autoplay on hover
        carousel.addEventListener('mouseenter', stopAutoplay);
        carousel.addEventListener('mouseleave', startAutoplay);
        
        // Add touch support for mobile
        let touchStartX = 0;
        let touchEndX = 0;
        
        carousel.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
            stopAutoplay();
        }, { passive: true });
        
        carousel.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
            startAutoplay();
        }, { passive: true });
        
        function handleSwipe() {
            const swipeThreshold = 50;
            if (touchEndX < touchStartX - swipeThreshold) {
                // Swipe left - show next slide
                showNextSlide();
            } else if (touchEndX > touchStartX + swipeThreshold) {
                // Swipe right - show previous slide
                showPrevSlide();
            }
        }
    }
    
    // Show a specific slide
    function showSlide(index) {
        // Hide all slides
        slides.forEach(slide => {
            slide.classList.remove('active');
        });
        
        // Deactivate all dots
        dots.forEach(dot => {
            dot.classList.remove('active');
        });
        
        // Show the selected slide
        slides[index].classList.add('active');
        
        // Activate the corresponding dot
        dots[index].classList.add('active');
        
        // Update current index
        currentIndex = index;
    }
    
    // Show the next slide
    function showNextSlide() {
        let nextIndex = currentIndex + 1;
        if (nextIndex >= slides.length) {
            nextIndex = 0;
        }
        showSlide(nextIndex);
        resetAutoplay();
    }
    
    // Show the previous slide
    function showPrevSlide() {
        let prevIndex = currentIndex - 1;
        if (prevIndex < 0) {
            prevIndex = slides.length - 1;
        }
        showSlide(prevIndex);
        resetAutoplay();
    }
    
    // Start autoplay
    function startAutoplay() {
        if (!interval) {
            interval = setInterval(showNextSlide, autoplayDelay);
        }
    }
    
    // Stop autoplay
    function stopAutoplay() {
        clearInterval(interval);
        interval = null;
    }
    
    // Reset autoplay
    function resetAutoplay() {
        stopAutoplay();
        startAutoplay();
    }
    
    // Initialize the carousel
    initCarousel();
    
    // Handle visibility change (tab switching)
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            stopAutoplay();
        } else {
            startAutoplay();
        }
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        // Adjust any responsive behavior if needed
    });
});
