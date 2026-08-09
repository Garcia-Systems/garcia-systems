export function initializeReadiness(root) {
    if (root.dataset.initialized) return;
    root.dataset.initialized = 'true';
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const reveal = () => root.classList.add('is-visible');
    if (reducedMotion || !('IntersectionObserver' in window)) reveal();
    else {
        const observer = new IntersectionObserver((entries) => {
            if (!entries.some((entry) => entry.isIntersecting)) return;
            reveal(); observer.disconnect();
        }, { threshold: 0.18 });
        observer.observe(root);
    }
    root.querySelectorAll('.gs-readiness-dimensions button').forEach((button) => {
        button.addEventListener('click', () => {
            const expanded = button.getAttribute('aria-expanded') === 'true';
            root.querySelectorAll('.gs-readiness-dimensions button').forEach((item) => item.setAttribute('aria-expanded', 'false'));
            button.setAttribute('aria-expanded', String(!expanded));
        });
    });
}
