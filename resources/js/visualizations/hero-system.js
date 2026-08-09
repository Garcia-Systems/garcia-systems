import * as THREE from 'three';

const NODE_DEFINITIONS = [
    { key: 'problem', position: [-3.7, 2.25, -0.6], color: 0x9a82ff, phase: 0.0 },
    { key: 'people', position: [-1.25, 3.15, -1.15], color: 0x5ee7f7, phase: 0.7 },
    { key: 'workflow', position: [-3.85, -0.15, 0.65], color: 0x4594ff, phase: 1.4 },
    { key: 'data', position: [-1.8, -2.75, -0.8], color: 0x5ee7f7, phase: 2.1 },
    { key: 'system', position: [3.25, 2.35, 0.4], color: 0x5ee7f7, phase: 2.8 },
    { key: 'automation', position: [4.05, 0.1, -0.7], color: 0x4594ff, phase: 3.5 },
    { key: 'outcome', position: [3.05, -2.55, 0.65], color: 0x9a82ff, phase: 4.2 },
];

const supportsWebGL = () => {
    try {
        const canvas = document.createElement('canvas');
        return Boolean(window.WebGLRenderingContext && (canvas.getContext('webgl2') || canvas.getContext('webgl')));
    } catch {
        return false;
    }
};

export function createHeroSystem(root) {
    if (!supportsWebGL()) {
        root.classList.add('is-fallback');
        return null;
    }

    const canvasHost = root.querySelector('[data-hero-system-canvas]');
    const labels = new Map([...root.querySelectorAll('[data-system-node]')]
        .map((label) => [label.dataset.systemNode, label]));
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(38, 1, 0.1, 50);
    camera.position.set(0, 0, 12.8);

    const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true, powerPreference: 'high-performance' });
    renderer.setClearColor(0x000000, 0);
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    canvasHost.append(renderer.domElement);

    const system = new THREE.Group();
    scene.add(system);
    scene.add(new THREE.AmbientLight(0x8cbcff, 0.65));
    const keyLight = new THREE.PointLight(0x5ee7f7, 24, 20);
    keyLight.position.set(1.5, 2, 5);
    scene.add(keyLight);
    const violetLight = new THREE.PointLight(0x9a82ff, 16, 18);
    violetLight.position.set(-4, -2, 3);
    scene.add(violetLight);

    const core = createCore();
    system.add(core.group);
    const nodeGeometry = new THREE.IcosahedronGeometry(0.22, 1);
    const nodes = NODE_DEFINITIONS.map((definition) => createNode(definition, nodeGeometry, system));
    const connections = nodes.map((node) => createConnection(node, system));
    const particles = createParticles(root.clientWidth < 640 ? 48 : 96);
    system.add(particles);

    const pointer = new THREE.Vector2();
    const targetRotation = new THREE.Vector2();
    const projected = new THREE.Vector3();
    const clock = new THREE.Clock();
    let frame = 0;
    let visible = true;
    let destroyed = false;

    const resize = () => {
        const width = Math.max(root.clientWidth, 1);
        const height = Math.max(root.clientHeight, 1);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, width < 640 ? 1.25 : 1.75));
        renderer.setSize(width, height, false);
        camera.aspect = width / height;
        camera.position.z = width < 520 ? 15.2 : width < 800 ? 14 : 12.8;
        camera.updateProjectionMatrix();
    };

    const positionLabels = () => {
        const width = root.clientWidth;
        const height = root.clientHeight;
        nodes.forEach(({ definition, marker }) => {
            marker.getWorldPosition(projected).project(camera);
            const label = labels.get(definition.key);
            label.style.setProperty('--node-x', `${(projected.x * 0.5 + 0.5) * width}px`);
            label.style.setProperty('--node-y', `${(-projected.y * 0.5 + 0.5) * height}px`);
            label.style.setProperty('--node-depth', `${THREE.MathUtils.clamp(1.05 - (projected.z + 1) * 0.12, 0.86, 1.04)}`);
        });
    };

    const render = () => {
        if (destroyed || !visible || document.hidden) return;
        const elapsed = clock.getElapsedTime();
        system.rotation.y += (targetRotation.x - system.rotation.y) * 0.025;
        system.rotation.x += (targetRotation.y - system.rotation.x) * 0.025;
        core.facets.rotation.y = elapsed * 0.075;
        core.rings[0].rotation.z = elapsed * 0.11;
        core.rings[1].rotation.x = Math.PI / 2.8 + elapsed * 0.07;
        nodes.forEach(({ marker, definition }, index) => {
            marker.position.y = definition.position[1] + Math.sin(elapsed * 0.42 + definition.phase) * 0.055;
            marker.rotation.y = elapsed * 0.16 + index;
        });
        connections.forEach(({ pulse, curve, phase }) => pulse.position.copy(curve.getPoint((elapsed * 0.075 + phase) % 1)));
        particles.rotation.y = elapsed * 0.012;
        renderer.render(scene, camera);
        positionLabels();
        frame = requestAnimationFrame(render);
    };

    const resume = () => {
        cancelAnimationFrame(frame);
        clock.getDelta();
        if (visible && !document.hidden) frame = requestAnimationFrame(render);
    };
    const onPointerMove = (event) => {
        const bounds = root.getBoundingClientRect();
        pointer.set((event.clientX - bounds.left) / bounds.width - 0.5, (event.clientY - bounds.top) / bounds.height - 0.5);
        targetRotation.set(pointer.x * 0.16, pointer.y * 0.1);
    };
    const onPointerLeave = () => targetRotation.set(0, 0);
    const onVisibility = () => resume();
    const resizeObserver = new ResizeObserver(resize);
    const visibilityObserver = new IntersectionObserver(([entry]) => {
        visible = entry.isIntersecting;
        resume();
    }, { threshold: 0.01 });

    resizeObserver.observe(root);
    visibilityObserver.observe(root);
    root.addEventListener('pointermove', onPointerMove, { passive: true });
    root.addEventListener('pointerleave', onPointerLeave);
    document.addEventListener('visibilitychange', onVisibility);
    resize();
    renderer.render(scene, camera);
    positionLabels();
    root.classList.add('is-three-ready');
    frame = requestAnimationFrame(render);

    return () => {
        destroyed = true;
        cancelAnimationFrame(frame);
        resizeObserver.disconnect();
        visibilityObserver.disconnect();
        document.removeEventListener('visibilitychange', onVisibility);
        root.removeEventListener('pointermove', onPointerMove);
        root.removeEventListener('pointerleave', onPointerLeave);
        scene.traverse((object) => {
            object.geometry?.dispose();
            if (Array.isArray(object.material)) object.material.forEach((material) => material.dispose());
            else object.material?.dispose();
        });
        renderer.dispose();
        renderer.domElement.remove();
    };
}

function createCore() {
    const group = new THREE.Group();
    const facets = new THREE.Mesh(
        new THREE.IcosahedronGeometry(1.05, 1),
        new THREE.MeshStandardMaterial({ color: 0x092a46, emissive: 0x087993, emissiveIntensity: 1.15, metalness: 0.72, roughness: 0.25, flatShading: true }),
    );
    const shell = new THREE.Mesh(
        new THREE.IcosahedronGeometry(1.34, 1),
        new THREE.MeshBasicMaterial({ color: 0x5ee7f7, wireframe: true, transparent: true, opacity: 0.24 }),
    );
    const ringMaterial = new THREE.MeshBasicMaterial({ color: 0x5ee7f7, transparent: true, opacity: 0.34, side: THREE.DoubleSide });
    const rings = [new THREE.Mesh(new THREE.TorusGeometry(1.72, 0.018, 8, 80), ringMaterial), new THREE.Mesh(new THREE.TorusGeometry(1.95, 0.012, 8, 80), ringMaterial.clone())];
    rings[0].rotation.x = Math.PI / 2.2;
    rings[1].rotation.x = Math.PI / 2.8;
    group.add(facets, shell, ...rings);
    return { group, facets, rings };
}

function createNode(definition, geometry, parent) {
    const marker = new THREE.Group();
    marker.position.set(...definition.position);
    const material = new THREE.MeshStandardMaterial({ color: 0x102943, emissive: definition.color, emissiveIntensity: 0.72, metalness: 0.5, roughness: 0.28 });
    const orb = new THREE.Mesh(geometry, material);
    const halo = new THREE.Mesh(new THREE.RingGeometry(0.3, 0.36, 24), new THREE.MeshBasicMaterial({ color: definition.color, transparent: true, opacity: 0.42, side: THREE.DoubleSide }));
    halo.rotation.x = Math.PI / 2;
    marker.add(orb, halo);
    parent.add(marker);
    return { definition, marker };
}

function createConnection(node, parent) {
    const end = node.marker.position.clone();
    const midpoint = end.clone().multiplyScalar(0.48);
    midpoint.z += end.x > 0 ? 0.75 : -0.55;
    const curve = new THREE.QuadraticBezierCurve3(new THREE.Vector3(), midpoint, end);
    const line = new THREE.Line(new THREE.BufferGeometry().setFromPoints(curve.getPoints(40)), new THREE.LineBasicMaterial({ color: node.definition.color, transparent: true, opacity: 0.34 }));
    const pulse = new THREE.Mesh(new THREE.SphereGeometry(0.055, 8, 8), new THREE.MeshBasicMaterial({ color: node.definition.color }));
    parent.add(line, pulse);
    return { curve, pulse, phase: node.definition.phase / 7 };
}

function createParticles(count) {
    const positions = new Float32Array(count * 3);
    for (let index = 0; index < count; index += 1) {
        const seed = index + 1;
        positions[index * 3] = Math.sin(seed * 12.9898) * 5.6;
        positions[index * 3 + 1] = Math.cos(seed * 7.233) * 4.1;
        positions[index * 3 + 2] = Math.sin(seed * 3.117) * 3.2 - 1;
    }
    const geometry = new THREE.BufferGeometry();
    geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
    return new THREE.Points(geometry, new THREE.PointsMaterial({ color: 0x75dfff, size: 0.025, transparent: true, opacity: 0.46, depthWrite: false }));
}
