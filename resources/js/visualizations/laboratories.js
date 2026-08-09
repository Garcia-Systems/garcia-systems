export function initializeLaboratories(root) {
    if (root.dataset.initialized) return;
    root.dataset.initialized = 'true';
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const cards = [...root.querySelectorAll('[data-laboratory]')];
    if (reducedMotion) return;
    const activate = (card) => {
        cards.forEach((item) => item.classList.remove('is-active'));
        card.classList.remove('is-running');
        card.classList.add('is-active');
        requestAnimationFrame(() => card.classList.add('is-running'));
    };
    cards.forEach((card) => {
        card.addEventListener('pointerenter', () => activate(card));
        card.addEventListener('focusin', () => activate(card));
        card.addEventListener('click', () => activate(card));
        card.addEventListener('animationend', () => card.classList.remove('is-running'));
    });
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            const card = entries.find((entry) => entry.isIntersecting)?.target;
            if (!card || document.visibilityState !== 'visible') return;
            activate(card); observer.disconnect();
        }, { threshold: 0.45 });
        cards.forEach((card) => observer.observe(card));
    }
}
