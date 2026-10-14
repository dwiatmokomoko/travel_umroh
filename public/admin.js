document.querySelectorAll('[data-password-toggle]').forEach(button => button.addEventListener('click', () => {
    const input = document.getElementById(button.dataset.passwordToggle);
    const show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    button.setAttribute('aria-pressed', String(show));
    button.textContent = show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi';
}));
document.querySelectorAll('[data-confirm]').forEach(form => form.addEventListener('submit', event => {
    if (!window.confirm(form.dataset.confirm)) event.preventDefault();
}));
const imageInput = document.querySelector('[data-image-input]');
let previewUrl;
imageInput?.addEventListener('change', () => {
    const preview = document.querySelector('[data-image-preview]');
    if (previewUrl) URL.revokeObjectURL(previewUrl);
    const file = imageInput.files[0];
    if (file && ['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
        previewUrl = URL.createObjectURL(file);
        preview.src = previewUrl;
        preview.hidden = false;
    }
});
const editor = document.querySelector('[data-editor]');
let dirty = false;
editor?.addEventListener('input', () => { dirty = true; });
editor?.addEventListener('change', () => { dirty = true; });
editor?.addEventListener('submit', () => { dirty = false; });
window.addEventListener('beforeunload', event => {
    if (dirty) { event.preventDefault(); event.returnValue = ''; }
});
