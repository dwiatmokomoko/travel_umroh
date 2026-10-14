<button type="button" class="chat-launcher button" id="chat-launcher" hidden aria-controls="safir-chat">Tanya Safir</button>
<dialog id="safir-chat" class="chat-dialog" aria-labelledby="chat-title">
    <header class="chat-header"><div><strong id="chat-title">Asisten Safir</strong><small>{{ config('services.openai.enabled') && config('services.openai.key') ? 'Asisten AI · dapat membuat kekeliruan' : 'Asisten virtual · informasi dasar' }}</small></div><button type="button" id="chat-close" aria-label="Tutup percakapan">×</button></header>
    <div class="chat-body">
        <p class="chat-intro">{{ $site->text('chatbot_welcome', 'Assalamu’alaikum! Tanyakan paket, fasilitas, atau persiapan umrah. Untuk konsultasi pribadi, pilih admin di bawah.') }}</p>
        <div class="chat-messages" id="chat-messages" role="log" aria-label="Percakapan" aria-live="polite" aria-relevant="additions"></div>
        <div class="chat-prompts"><button type="button" data-chat-prompt="Apa pilihan paket dan harga umrah?">Paket & harga</button><button type="button" data-chat-prompt="Apa fasilitas paket umrah?">Fasilitas</button><button type="button" data-chat-prompt="Di mana alamat kantor?">Alamat kantor</button></div>
        <p id="chat-status" role="status"></p>
        <form id="chat-form" action="{{ route('chat.store') }}" method="post">@csrf<label for="chat-message">Pertanyaan Anda</label><div class="chat-input-row"><input id="chat-message" name="message" required maxlength="1500" autocomplete="off" placeholder="Tulis pertanyaan…"><button class="button small" type="submit">Kirim</button></div></form>
        <p class="fine">Saat AI aktif, pertanyaan dan 6 pesan terakhir diproses oleh OpenAI. Hindari data paspor, kesehatan, dan pembayaran. Riwayat di layar hilang saat halaman dimuat ulang.</p>
        <button type="button" class="chat-clear" id="chat-clear">Hapus percakapan</button>
        <h3>Lanjutkan dengan admin</h3>
        @include('components.consultants')
        <a class="text-link" href="{{ route('packages.index') }}">Lihat semua paket</a>
    </div>
</dialog>
