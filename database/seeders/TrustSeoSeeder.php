<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrustSeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            foreach ([
                'home_title' => 'Travel & Agen Umroh Jogja | Paket Umrah Safir Yogyakarta',
                'home_description' => 'Mencari travel atau agen umroh Jogja? Kenali Safir, bandingkan paket umrah, harga, jadwal dan fasilitas. Konsultasi Wisnu di Bantul untuk seluruh Indonesia.',
            ] as $key => $previous) {
                SiteSetting::firstOrCreate(['key' => $key], ['value' => config('seo.'.$key)]);
                SiteSetting::where('key', $key)->where('value', $previous)->update(['value' => config('seo.'.$key)]);
            }
            $page = Page::where('slug', 'umrah-jogja')->first();
            if (! $page || SiteSetting::where('key', 'seed_trust_seo_v1')->exists()) {
                return;
            }
            $page->body .= "\n\n".<<<'MD'
## Memilih agen, travel, atau biro umroh yang aman dan amanah
Mencari biro umroh Jogja yang aman dan amanah perlu disertai pemeriksaan informasi sebelum memesan. Kenali badan usaha penyelenggara, periksa dokumen legalitas, dan minta penawaran tertulis untuk paket pilihan Anda. Pelajari [profil dan informasi legalitas Safir](/tentang-kami), lalu konfirmasikan rincian kepada tim.

### Apa yang perlu diperiksa sebelum pembayaran?
- Nama penyelenggara dan penerima pembayaran sesuai dokumen pemesanan.
- Harga total, fasilitas, hotel, maskapai, jumlah penghuni kamar, serta biaya tambahan dijelaskan tertulis.
- Tanggal perjalanan dan status konfirmasi layanan dijelaskan sebelum pelunasan.
- Ketentuan perubahan, pembatalan, dan pengembalian dana dapat dibaca sebelum transaksi.
- Bukti pembayaran dan kontak penanggung jawab disimpan oleh jamaah.

### Bagaimana merencanakan bulan keberangkatan?
Gunakan [kalender perencanaan umrah lima tahun](/paket-umrah#planning-title) untuk memilih bulan, lalu periksa paket yang sudah dipublikasikan. Bulan yang belum memiliki jadwal dapat dikonsultasikan; pencantuman bulan bukan jaminan keberangkatan atau harga tetap. Siapkan jumlah jamaah, kota asal, anggaran, dan pilihan kamar saat [menghubungi tim Safir](/kontak).
MD;
            $page->save();
            SiteSetting::create(['key' => 'seed_trust_seo_v1', 'value' => '1']);
        });
    }
}
