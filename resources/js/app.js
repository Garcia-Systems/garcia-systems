const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const reveals = document.querySelectorAll('[data-reveal]');
const heroSystem = document.querySelector('[data-hero-system]');
const scrollStories = document.querySelectorAll('[data-friction-system], [data-process-flow]');

if (heroSystem && !reduceMotion) {
    const initializeHero = () => import('./visualizations/hero-system.js')
        .then(({ createHeroSystem }) => createHeroSystem(heroSystem))
        .catch(() => heroSystem.classList.add('is-fallback'));

    if ('IntersectionObserver' in window) {
        const heroObserver = new IntersectionObserver((entries) => {
            if (!entries.some((entry) => entry.isIntersecting)) return;
            heroObserver.disconnect();
            initializeHero();
        }, { rootMargin: '240px' });
        heroObserver.observe(heroSystem);
    } else {
        initializeHero();
    }
}

if (!reduceMotion && scrollStories.length) {
    document.documentElement.classList.add('gs-motion-ready');
    let frameRequested = false;

    const updateStories = () => {
        const viewportHeight = window.innerHeight;
        scrollStories.forEach((story) => {
            const bounds = story.getBoundingClientRect();
            const travel = viewportHeight + bounds.height;
            const isProcessFlow = story.matches('[data-process-flow]');
            let progress = (viewportHeight - bounds.top) / travel;

            if (isProcessFlow && window.innerWidth >= 768) {
                // A horizontal row is much shorter than the mobile stack. Complete its
                // sequence while the row moves from 82% to 35% of the viewport, leaving
                // the remaining visible scroll distance to register the completed state.
                const desktopStart = viewportHeight * 0.82;
                const desktopEnd = viewportHeight * 0.35;
                progress = (desktopStart - bounds.top) / (desktopStart - desktopEnd);
            }

            progress = Math.min(1, Math.max(0, progress));
            story.style.setProperty(isProcessFlow ? '--process-progress' : '--story-progress', progress.toFixed(3));
        });
        frameRequested = false;
    };

    const requestStoryUpdate = () => {
        if (frameRequested) return;
        frameRequested = true;
        window.requestAnimationFrame(updateStories);
    };

    updateStories();
    window.addEventListener('scroll', requestStoryUpdate, { passive: true });
    window.addEventListener('resize', requestStoryUpdate, { passive: true });
    window.addEventListener('pageshow', requestStoryUpdate);
}

if (!reduceMotion && 'IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });
    reveals.forEach((element) => observer.observe(element));
} else {
    reveals.forEach((element) => element.classList.add('is-visible'));
}

document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;
    document.querySelectorAll('details[open]').forEach((menu) => menu.removeAttribute('open'));
});
