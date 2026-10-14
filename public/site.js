const menuButton = document.querySelector('.menu-toggle');
const navigation = document.querySelector('#navigation');
const mobileViewport = window.matchMedia('(max-width: 960px)');
const closeMenu = () => {
    menuButton?.setAttribute('aria-expanded', 'false');
    navigation?.classList.remove('is-open');
};
if (menuButton && navigation) menuButton.hidden = false;
menuButton?.addEventListener('click', () => {
    const expanded = menuButton.getAttribute('aria-expanded') === 'true';
    menuButton.setAttribute('aria-expanded', String(!expanded));
    navigation.classList.toggle('is-open', !expanded);
});
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && menuButton?.getAttribute('aria-expanded') === 'true') {
        closeMenu();
        menuButton.focus();
    }
});
document.addEventListener('click', (event) => {
    if (!event.target.closest('.nav-wrap')) closeMenu();
});
document.addEventListener('focusin', (event) => {
    if (!event.target.closest('.nav-wrap')) closeMenu();
});
navigation?.addEventListener('click', (event) => {
    if (event.target.closest('a')) closeMenu();
});
mobileViewport.addEventListener('change', closeMenu);
window.addEventListener('pageshow', closeMenu);

const consultationForm = document.querySelector('#consultation-form');
consultationForm?.querySelectorAll('input[name="name"], input[name="city"]').forEach((input) => {
    const validate = () => input.setCustomValidity(input.value.trim() ? '' : 'Mohon isi kolom ini, bukan hanya spasi.');
    input.addEventListener('input', validate);
    validate();
});
document.querySelector('#consultation-form')?.addEventListener('submit', (event) => {
    event.preventDefault();
    const form = event.currentTarget;
    if (!form.reportValidity()) return;
    const data = new FormData(form);
    const message = `Assalamu’alaikum, saya ${data.get('name').trim()} dari ${data.get('city').trim()}.\nSaya ingin konsultasi untuk ${data.get('people')} jamaah.\nProgram: ${data.get('program')}.\n${String(data.get('message') || '').trim()}`;
    window.location.assign(`https://wa.me/${data.get('consultant')}?text=${encodeURIComponent(message)}`);
});
