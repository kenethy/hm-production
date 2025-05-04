document.addEventListener('DOMContentLoaded', function() {
    const track = document.querySelector('.testimonial-track');
    if (!track) return;
    
    const slides = document.querySelectorAll('.testimonial-slide');
    const slideCount = slides.length;
    const slideWidth = 100 / slideCount;
    
    // Set CSS variables for animation
    track.style.setProperty('--slide-count', slideCount);
    track.style.setProperty('--scroll-duration', slideCount * 5 + 's'); // 5 seconds per slide
    
    // Clone slides for infinite scrolling
    slides.forEach(slide => {
        const clone = slide.cloneNode(true);
        track.appendChild(clone);
    });
    
    // Set width of track based on number of slides (including clones)
    track.style.width = `${slideCount * 2 * 100}%`;
    
    // Set width of each slide
    document.querySelectorAll('.testimonial-slide').forEach(slide => {
        slide.style.width = `${slideWidth}%`;
    });
    
    // Start scrolling animation after a delay
    setTimeout(() => {
        track.classList.add('scrolling');
    }, 1000);
    
    // Reset animation when it completes
    track.addEventListener('animationend', () => {
        track.classList.remove('scrolling');
        track.style.transform = 'translateX(0)';
        setTimeout(() => {
            track.classList.add('scrolling');
        }, 10);
    });
    
    // Pause animation on hover
    track.addEventListener('mouseenter', () => {
        track.style.animationPlayState = 'paused';
    });
    
    track.addEventListener('mouseleave', () => {
        track.style.animationPlayState = 'running';
    });
});
