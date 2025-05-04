/**
 * Hartono Motor - Animation Scripts
 * Smooth, lightweight animations for a professional look
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize scroll animations
    initScrollAnimations();
    
    // Initialize staggered text animations
    initStaggeredText();
    
    // Initialize smooth page transitions
    initPageTransitions();
});

/**
 * Initialize scroll reveal animations
 */
function initScrollAnimations() {
    const revealElements = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-up');
    
    // Initial check for elements in viewport
    checkReveal();
    
    // Add scroll event listener
    window.addEventListener('scroll', throttle(checkReveal, 100));
    
    function checkReveal() {
        const windowHeight = window.innerHeight;
        const revealPoint = 150; // How many pixels from the bottom of the viewport to start revealing
        
        revealElements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            
            if (elementTop < windowHeight - revealPoint) {
                element.classList.add('active');
            }
        });
    }
}

/**
 * Initialize staggered text animations
 */
function initStaggeredText() {
    const staggerTextElements = document.querySelectorAll('.stagger-text');
    
    staggerTextElements.forEach(element => {
        const text = element.textContent;
        element.textContent = '';
        
        // Create spans for each character
        for (let i = 0; i < text.length; i++) {
            const span = document.createElement('span');
            span.textContent = text[i];
            span.style.animationDelay = `${i * 0.03}s`;
            element.appendChild(span);
        }
    });
}

/**
 * Initialize smooth page transitions
 */
function initPageTransitions() {
    // Add transition class to body
    document.body.classList.add('page-transition');
    
    // Add click event to all internal links
    const internalLinks = document.querySelectorAll('a[href^="/"]:not([target="_blank"])');
    
    internalLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            // Skip if modifier keys are pressed
            if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
            
            // Skip if it's a different domain
            if (href.indexOf(window.location.origin) !== 0 && href.indexOf('/') !== 0) return;
            
            e.preventDefault();
            
            // Add transitioning class
            document.body.classList.add('transitioning');
            
            // Navigate after transition
            setTimeout(() => {
                window.location.href = href;
            }, 300);
        });
    });
}

/**
 * Throttle function to limit how often a function can be called
 */
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

/**
 * Add animation classes to elements when they enter viewport
 * @param {string} selector - CSS selector for elements to animate
 * @param {string} animationClass - CSS class to add for animation
 */
function animateOnScroll(selector, animationClass) {
    const elements = document.querySelectorAll(selector);
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add(animationClass);
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });
    
    elements.forEach(element => {
        observer.observe(element);
    });
}

// Export functions for use in other scripts
window.HartonoAnimations = {
    animateOnScroll,
    initScrollAnimations,
    initStaggeredText
};
