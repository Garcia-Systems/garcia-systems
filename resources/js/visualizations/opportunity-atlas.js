const SVG_NS = 'http://www.w3.org/2000/svg';

export function initializeOpportunityAtlas(map) {
    const stage = map.querySelector('[data-atlas-stage]');
    const svg = map.querySelector('[data-atlas-connections]');
    const nodes = [...map.querySelectorAll('[data-atlas-node]')];
    const edgeDefinitions = [...map.querySelectorAll('[data-atlas-edge]')];
    const reset = map.querySelector('[data-atlas-reset]');
    let lockedChains = null;
    let resizeFrame;

    const setActive = (chains = null) => {
        const active = chains ? new Set(chains) : null;
        stage?.classList.toggle('has-active-path', Boolean(active));
        nodes.forEach((node) => {
            const connected = node.dataset.atlasChains.split(',').some((chain) => active?.has(chain));
            node.classList.toggle('is-related', Boolean(active && connected));
            node.setAttribute('aria-pressed', String(Boolean(lockedChains && connected)));
        });
        svg?.querySelectorAll('[data-chain]').forEach((path) => path.classList.toggle('is-related', Boolean(active?.has(path.dataset.chain))));
    };

    const drawConnections = () => {
        if (!stage || !svg || stage.offsetParent === null) return;
        const stageBox = stage.getBoundingClientRect();
        svg.replaceChildren();
        svg.setAttribute('viewBox', `0 0 ${stageBox.width} ${stageBox.height}`);

        edgeDefinitions.forEach((edge) => {
            const from = map.querySelector(`[data-atlas-node="${edge.dataset.from}"]`);
            const to = map.querySelector(`[data-atlas-node="${edge.dataset.to}"]`);
            if (!from || !to) return;
            const a = from.getBoundingClientRect();
            const b = to.getBoundingClientRect();
            const x1 = a.right - stageBox.left;
            const y1 = a.top + a.height / 2 - stageBox.top;
            const x2 = b.left - stageBox.left;
            const y2 = b.top + b.height / 2 - stageBox.top;
            const bend = Math.max(18, (x2 - x1) * .45);
            const path = document.createElementNS(SVG_NS, 'path');
            path.setAttribute('d', `M ${x1} ${y1} C ${x1 + bend} ${y1}, ${x2 - bend} ${y2}, ${x2} ${y2}`);
            path.dataset.chain = edge.dataset.chain;
            svg.append(path);
        });
        setActive(lockedChains);
    };

    nodes.forEach((node) => {
        const chains = node.dataset.atlasChains.split(',');
        node.addEventListener('mouseenter', () => { if (!lockedChains) setActive(chains); });
        node.addEventListener('mouseleave', () => { if (!lockedChains) setActive(); });
        node.addEventListener('focus', () => { if (!lockedChains) setActive(chains); });
        node.addEventListener('blur', () => { if (!lockedChains) setActive(); });
        node.addEventListener('click', () => {
            lockedChains = lockedChains?.join(',') === chains.join(',') ? null : chains;
            reset.hidden = !lockedChains;
            setActive(lockedChains);
        });
    });

    reset?.addEventListener('click', () => {
        lockedChains = null;
        reset.hidden = true;
        setActive();
    });

    const mobileTabs = [...map.querySelectorAll('[data-atlas-mobile-tab]')];
    const selectMobileTab = (tab) => {
        mobileTabs.forEach((item) => {
            item.setAttribute('aria-selected', String(item === tab));
            item.tabIndex = item === tab ? 0 : -1;
        });
        map.querySelectorAll('.gs-atlas-mobile-path').forEach((panel) => { panel.hidden = panel.id !== `atlas-path-${tab.dataset.atlasMobileTab}`; });
    };
    mobileTabs.forEach((tab, index) => {
        tab.tabIndex = index === 0 ? 0 : -1;
        tab.addEventListener('click', () => selectMobileTab(tab));
        tab.addEventListener('keydown', (event) => {
            if (!['ArrowLeft', 'ArrowRight'].includes(event.key)) return;
            event.preventDefault();
            const direction = event.key === 'ArrowRight' ? 1 : -1;
            const next = mobileTabs[(index + direction + mobileTabs.length) % mobileTabs.length];
            selectMobileTab(next);
            next.focus();
        });
    });

    const requestDraw = () => {
        cancelAnimationFrame(resizeFrame);
        resizeFrame = requestAnimationFrame(drawConnections);
    };
    if ('ResizeObserver' in window && stage) new ResizeObserver(requestDraw).observe(stage);
    else window.addEventListener('resize', requestDraw, { passive: true });
    requestDraw();

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(([entry]) => {
            map.classList.toggle('is-in-view', entry.isIntersecting);
            if (entry.isIntersecting) {
                map.classList.add('is-visible');
                requestDraw();
            }
        }, { threshold: .12 });
        observer.observe(map);
    } else {
        map.classList.add('is-visible');
    }
}
