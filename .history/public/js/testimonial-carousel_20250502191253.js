document.addEventListener('DOMContentLoaded', function () {
    const track = document.querySelector('.testimonial-track');
    if (!track) return;

    // Clone the grid twice to create a smooth infinite loop
    const originalGrid = document.querySelector('.testimonial-grid');
    if (!originalGrid) return;

    const clone1 = originalGrid.cloneNode(true);
    const clone2 = originalGrid.cloneNode(true);
    track.appendChild(clone1);
    track.appendChild(clone2);

    // Handle animation restart to create seamless loop
    track.addEventListener('animationiteration', function () {
        // This ensures the animation remains smooth between iterations
        console.log('Animation iteration completed');
    });

    // Adjust animation speed based on screen size
    function adjustAnimationSpeed() {
        const width = window.innerWidth;
        let duration = '15s'; // Default for desktop (3 columns)

        if (width <= 768) {
            duration = '10s'; // Mobile (1 column)
        } else if (width <= 1023) {
            duration = '12s'; // Tablet (2 columns)
        }

        track.style.animationDuration = duration;
    }

    // Initial adjustment
    adjustAnimationSpeed();

    // Adjust on window resize
    window.addEventListener('resize', adjustAnimationSpeed);

    // Pause animation on hover
    track.addEventListener('mouseenter', () => {
        track.style.animationPlayState = 'paused';
    });

    track.addEventListener('mouseleave', () => {
        track.style.animationPlayState = 'running';
    });
});
