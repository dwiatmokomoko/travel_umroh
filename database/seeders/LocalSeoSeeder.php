<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class LocalSeoSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'home_title' => 'Safir Jogja | Paket Umrah & Travel Umrah Yogyakarta',
            'home_description' => 'Rencanakan umrah bersama Safir Caturjaya Wisata Jogja. Paket 9 dan 12 hari, konsultasi di Bantul, melayani calon jamaah seluruh Indonesia.',
        ] as $key => $previous) {
            SiteSetting::where('key', $key)->where('value', $previous)->update(['value' => config('seo.'.$key)]);
        }
        $page = Page::where('slug', 'umrah-jogja')->first();
        if (! $page) {
            return;
        }
        foreach (['title', 'seo_title'] as $field) {
            if ($page->$field === 'Travel Umrah Jogja, Bantul & seluruh Indonesia') {
                $page->$field = 'Travel Umroh Jogja: Konsultasi Umrah di Bantul';
            }
        }
        if (! str_contains($page->body, '## Memilih paket umrah atau umroh Jogja')) {
            $page->body .= <<<'MD'


## Memilih paket umrah atau umroh Jogja
Safir melayani konsultasi perjalanan umrah bagi calon jamaah di Yogyakarta, Bantul, Sleman, Kulon Progo, Gunungkidul, dan seluruh Indonesia. Istilah “umroh Jogja” juga sering digunakan untuk mencari layanan yang sama. Kantor konsultasi berada di Bantul; calon jamaah luar daerah dapat berkonsultasi melalui WhatsApp.

### Bagaimana membandingkan harga paket?
Buka [katalog paket umrah](/paket-umrah) untuk melihat jadwal, durasi, harga, hotel, dan maskapai yang sedang dipublikasikan. Harga tertera perlu dikonfirmasi bersama ketersediaan kursi dan rincian layanan sebelum pemesanan.

### Apakah penerbangannya dari YIA?
Lokasi pelayanan di Jogja tidak berarti semua paket berangkat dari YIA. Tanyakan bandara keberangkatan, rute penerbangan, titik temu rombongan, dan biaya perjalanan penghubung untuk paket pilihan Anda.

### Siapa yang bisa dihubungi?
Kontak utama adalah {contact}, melalui {phone}. Pilih Wisnu, Ryan, atau Dwi pada [halaman konsultasi](/kontak#konsultan). Buat janji sebelum datang ke kantor Bantul di {address}.

### Bagaimana memeriksa legalitas dan persiapan?
Lihat [profil perusahaan dan dokumen legalitas](/tentang-kami), lalu minta tim menunjukkan dokumen izin serta konfirmasi status terbaru. Baca [panduan persiapan umrah](/panduan-umrah) untuk memahami hal yang perlu ditanyakan sebelum mendaftar.
MD;
        }
        if ($page->isDirty()) {
            $page->save();
        }
    }
}
