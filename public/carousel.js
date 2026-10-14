document.querySelectorAll('[data-carousel]').forEach((carousel) => {
    const slides = [...carousel.querySelectorAll('[data-slide]')];
    if (slides.length < 2) return;
    const controls = carousel.querySelector('[data-carousel-controls]');
    const pause = carousel.querySelector('[data-pause]');
    const dots = [...carousel.querySelectorAll('[data-go]')];
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let current = 0;
    let paused = reducedMotion.matches;
    let timer;
    const stop = () => clearInterval(timer);
    const show = (index) => {
        current = (index + slides.length) % slides.length;
        slides.forEach((slide, i) => { slide.hidden = i !== current; });
        dots.forEach((dot, i) => dot.setAttribute('aria-pressed', String(i === current)));
    };
    const schedule = () => {
        stop();
        if (!paused && !document.hidden && !carousel.matches(':hover') && !carousel.contains(document.activeElement)) timer = setInterval(() => show(current + 1), 6500);
    };
    const updatePause = () => {
        pause.textContent = paused ? 'Putar' : 'Jeda';
        pause.setAttribute('aria-label', paused ? 'Mulai pergantian slide' : 'Jeda pergantian slide');
        schedule();
    };
    const navigate = (index) => { show(index); paused = true; updatePause(); };
    carousel.querySelector('[data-previous]').addEventListener('click', () => navigate(current - 1));
    carousel.querySelector('[data-next]').addEventListener('click', () => navigate(current + 1));
    dots.forEach((dot, i) => dot.addEventListener('click', () => navigate(i)));
    pause.addEventListener('click', () => { paused = !paused; updatePause(); });
    carousel.addEventListener('mouseenter', stop);
    carousel.addEventListener('mouseleave', schedule);
    carousel.addEventListener('focusin', () => { paused = true; updatePause(); });
    carousel.addEventListener('focusout', () => setTimeout(schedule, 0));
    document.addEventListener('visibilitychange', schedule);
    reducedMotion.addEventListener('change', () => { paused = reducedMotion.matches; updatePause(); });
    controls.hidden = false;
    updatePause();
});
