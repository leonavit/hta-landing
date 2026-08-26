const container = document.querySelector('.hta-topics-shader');

if (container) {
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let gradient = null;
    let loading = false;

    const options = {
        animate: reduced ? 'off' : 'on',
        axesHelper: 'off',
        brightness: 0.8,
        cAzimuthAngle: 180,
        cDistance: 3.6,
        cPolarAngle: 90,
        cameraZoom: 1,
        color1: '#1e293b',
        color2: '#1e293b',
        color3: '#22d3ee',
        destination: 'onCanvas',
        embedMode: 'off',
        enableCameraControls: false,
        envPreset: 'city',
        format: 'gif',
        fov: 30,
        frameRate: 10,
        gizmoHelper: 'hide',
        grain: 'off',
        lightType: '3d',
        manualRender: reduced,
        pixelDensity: 3,
        positionX: -1.4,
        positionY: 0,
        positionZ: 0,
        range: 'enabled',
        rangeEnd: 40,
        rangeStart: 0,
        reflection: 0.1,
        rotationX: 0,
        rotationY: 10,
        rotationZ: 50,
        shader: 'defaults',
        type: 'waterPlane',
        uAmplitude: 1,
        uDensity: 0.9,
        uFrequency: 5.5,
        uSpeed: 0.2,
        uStrength: 1.3,
        uTime: 0,
        wireframe: false,
    };

    async function mount() {
        if (gradient || loading || !container.clientWidth || !container.clientHeight) {
            return;
        }

        loading = true;

        try {
            const { ShaderGradient } = await import('https://esm.sh/@shader-gradient/core@0.3.0');
            if (!container.clientWidth || !container.clientHeight) {
                return;
            }
            gradient = new ShaderGradient(container, options);
        } catch (error) {
            console.warn('HTA topics shader failed to load', error);
        } finally {
            loading = false;
        }
    }

    function unmount() {
        if (!gradient) {
            return;
        }

        gradient.dispose();
        gradient = null;
    }

    const observer = new IntersectionObserver(
        function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    mount();
                }
            });
        },
        { rootMargin: '240px 0px' }
    );

    observer.observe(container);
    window.addEventListener('pagehide', unmount);
}
