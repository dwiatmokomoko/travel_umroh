const chatDialog = document.querySelector('#safir-chat');
const chatLauncher = document.querySelector('#chat-launcher');
if (chatDialog && chatLauncher && typeof chatDialog.showModal === 'function') {
    const form = document.querySelector('#chat-form');
    const input = document.querySelector('#chat-message');
    const messages = document.querySelector('#chat-messages');
    const status = document.querySelector('#chat-status');
    const submit = form.querySelector('button[type="submit"]');
    const clear = document.querySelector('#chat-clear');
    let history = [];
    let pending = false;
    chatLauncher.hidden = false;
    chatLauncher.addEventListener('click', () => { chatDialog.showModal(); input.focus(); });
    document.querySelector('#chat-close').addEventListener('click', () => chatDialog.close());
    chatDialog.addEventListener('close', () => chatLauncher.focus());
    clear.addEventListener('click', () => { history = []; messages.replaceChildren(); status.textContent = ''; input.focus(); });
    const appendMessage = (role, text) => {
        const bubble = document.createElement('p');
        bubble.className = `chat-bubble chat-${role}`;
        const label = document.createElement('strong');
        label.textContent = role === 'user' ? 'Anda' : 'Asisten Safir';
        const body = document.createElement('span');
        body.textContent = text;
        bubble.append(label, body);
        messages.append(bubble);
        while (messages.children.length > 20) messages.firstElementChild.remove();
        bubble.scrollIntoView({ block: 'nearest' });
    };
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const message = input.value.trim();
        if (pending || !message || !form.reportValidity()) return;
        pending = true;
        submit.disabled = true;
        clear.disabled = true;
        input.value = '';
        appendMessage('user', message);
        status.textContent = 'Menyiapkan jawaban…';
        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 35000);
        try {
            const response = await fetch(form.action, {
                method: 'POST', credentials: 'same-origin', signal: controller.signal,
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value },
                body: JSON.stringify({ message, history: history.slice(-6) }),
            });
            if (!response.ok) {
                const errors = { 419: 'Sesi berakhir. Muat ulang halaman untuk melanjutkan.', 429: 'Terlalu banyak pertanyaan. Tunggu satu menit atau hubungi admin.', 404: 'Chat sedang dinonaktifkan. Silakan hubungi admin.' };
                throw new Error(errors[response.status] || 'Pesan belum dapat diproses. Coba kembali atau hubungi admin.');
            }
            const data = await response.json();
            if (typeof data.answer !== 'string') throw new Error('Jawaban belum tersedia. Silakan hubungi admin.');
            appendMessage('assistant', data.answer);
            history.push({ role: 'user', content: message }, { role: 'assistant', content: data.answer });
            history = history.slice(-6);
            status.textContent = data.mode === 'ai' ? 'Jawaban AI · konfirmasikan rincian ke admin.' : 'Jawaban informasi dasar situs · bukan jawaban AI.';
        } catch (error) {
            status.textContent = error.name === 'AbortError' ? 'Waktu tunggu habis. Coba lagi atau hubungi admin.' : (error instanceof TypeError ? 'Koneksi terputus. Periksa internet Anda dan coba lagi.' : error.message);
            input.value = message;
        } finally {
            clearTimeout(timeout);
            pending = false;
            submit.disabled = false;
            clear.disabled = false;
            if (chatDialog.open) input.focus();
        }
    });
    document.querySelectorAll('[data-chat-prompt]').forEach((button) => button.addEventListener('click', () => {
        if (pending) return;
        input.value = button.dataset.chatPrompt;
        form.requestSubmit();
    }));
}
