# Garcia Systems visual system

The Phase 1 foundation lives in `resources/css/app.css`. The `--gs-*` custom properties define the dark surfaces, text hierarchy, cyan/blue/violet accents, borders, radii, shadows, and transition timing. Shared `.gs-*` component classes provide shells, section rhythm, glass panels, cards, buttons, links, focus treatment, and reduced-motion-safe reveals. Blade components should use these patterns instead of introducing page-specific equivalents.

The homepage hero's `<x-system-visual>` is intentionally a lightweight CSS/SVG placeholder. In Phase 2, its outer component boundary is the integration point for the Three.js canvas; preserve the hero grid and accessible graphic label while replacing the component interior. Three.js, WebGL, particle effects, and scroll storytelling are intentionally out of scope for Phase 1.
