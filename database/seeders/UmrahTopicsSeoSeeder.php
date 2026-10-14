<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UmrahTopicsSeoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            foreach ([
                'home_title' => 'Travel Umroh Jogja & Paket Umrah | Safir Yogyakarta',
                'home_description' => 'Cari paket umrah atau umroh Jogja? Bandingkan jadwal, harga, hotel, dan fasilitas Safir. Konsultasi dengan Wisnu di Bantul untuk jamaah seluruh Indonesia.',
                'packages_title' => 'Paket Umrah dari Jogja & Seluruh Indonesia | Safir',
                'packages_description' => 'Bandingkan jadwal dan harga paket umrah Safir Jogja. Pilihan program, hotel, dan maskapai untuk perjalanan ibadah Anda.',
            ] as $key => $previous) {
                SiteSetting::firstOrCreate(['key' => $key], ['value' => config('seo.'.$key)]);
                SiteSetting::where('key', $key)->where('value', $previous)->update(['value' => config('seo.'.$key)]);
            }
            $sections = [
                'umrah-jogja' => <<<'MD'
## Travel umroh Jogja dan agen umroh Jogja: mulai konsultasi dari sini
Sedang mencari travel umrah Yogyakarta, biro perjalanan umroh, atau agen umroh terdekat di Jogja? Safir menyediakan informasi paket dan konsultasi untuk membantu calon jamaah membandingkan rencana perjalanan. Kantor konsultasi berada di Bantul, dengan layanan WhatsApp bagi jamaah dari seluruh DIY dan Indonesia.

### Pilih informasi sesuai kebutuhan Anda
- **Biaya dan harga umroh:** lihat [pilihan paket berdasarkan harga](/paket-umrah?sort=price), lalu minta rincian biaya yang sudah termasuk dan biaya tambahan.
- **Jadwal keberangkatan dan durasi:** periksa [katalog paket umrah](/paket-umrah) untuk tanggal, lama perjalanan, dan maskapai. Konfirmasikan kursi dan rute sebelum memesan.
- **Hotel dan fasilitas:** bandingkan hotel Makkah dan Madinah, pembagian kamar, konsumsi, transportasi, serta pendampingan yang tercantum pada detail paket.
- **Paspor, visa, dan persiapan:** baca [panduan umrah](/panduan-umrah), lalu tanyakan persyaratan terbaru kepada tim sesuai kondisi jamaah dan tanggal perjalanan.
- **Legalitas travel dan pengalaman jamaah:** pelajari [profil Safir](/tentang-kami) dan [testimoni yang mendapat izin publikasi](/testimoni).
- **Perbandingan agen perjalanan:** gunakan [panduan membandingkan travel umroh Jogja](/perbandingan-travel-umroh-jogja) untuk meminta penawaran dengan rincian yang setara.

### Umrah keluarga, lansia, dan jamaah pertama kali
Sampaikan jumlah peserta, usia, kebutuhan mobilitas, pilihan kamar, dan pengalaman perjalanan kepada tim konsultasi. Tanyakan pendampingan, jarak hotel, serta kebutuhan khusus yang dapat dipenuhi pada paket pilihan Anda. Hindari mengirim informasi kesehatan sensitif ke chatbot umum.

### Umroh Ramadhan, liburan, dan program tambahan
Ketersediaan program Ramadhan, liburan sekolah, atau perjalanan tambahan mengikuti jadwal yang benar-benar diterbitkan di katalog. Jika belum ada paket yang sesuai, [hubungi Wisnu, Ryan, atau Dwi](/kontak#konsultan) untuk menanyakan rencana terbaru; penyebutan program ini bukan konfirmasi bahwa paket sudah tersedia.
MD,
                'panduan-umrah' => <<<'MD'
## Pertanyaan sebelum memilih paket umrah

### Apa yang perlu ditanyakan tentang paspor dan visa umroh?
Mintalah daftar dokumen dan batas waktu pengumpulan kepada tim sebelum memesan. Pastikan nama pada dokumen perjalanan sesuai, dan tanyakan proses pengurusan visa untuk program yang dipilih. Persyaratan administrasi dan kesehatan harus dikonfirmasi berdasarkan ketentuan terbaru; halaman ini bukan daftar persyaratan resmi yang selalu mutakhir.

### Bagaimana mempersiapkan manasik dan perlengkapan umrah?
Konfirmasikan jadwal serta tempat manasik, materi bimbingan, perlengkapan yang diberikan travel, barang pribadi yang perlu dibawa, dan batas bagasi maskapai. Tanyakan nomor pendamping dan titik temu rombongan sebelum keberangkatan.

### Bagaimana menghitung anggaran perjalanan dari Jogja?
Mulai dari harga paket tertulis, kemudian periksa biaya dokumen, kebutuhan pribadi, pembagian kamar, serta transportasi ke bandara atau titik temu. Bandingkan penawaran untuk tanggal dan fasilitas yang setara, bukan hanya nominal promonya.

### Apa yang harus ada dalam konfirmasi perjalanan?
Minta rincian tanggal pergi-pulang, maskapai dan rute, hotel dan jumlah malam, ketentuan layanan, jadwal pelunasan, serta kebijakan perubahan dan pembatalan. Pastikan rincian penting disepakati tertulis sebelum transaksi.

Lanjutkan dengan [melihat paket dan jadwal umrah](/paket-umrah), [mengenal layanan travel dan agen umroh Jogja](/umrah-jogja), atau [konsultasi langsung dengan tim Safir](/kontak#konsultan).
MD,
            ];
            foreach ($sections as $slug => $body) {
                $page = Page::where('slug', $slug)->first();
                $marker = 'seed_umrah_topics_v1_'.$slug;
                if (! $page || SiteSetting::where('key', $marker)->exists()) {
                    continue;
                }
                $page->body .= "\n\n".$body;
                if ($slug === 'umrah-jogja' && $page->seo_title === 'Travel Umroh Jogja: Harga, Jadwal & Layanan Seluruh DIY') {
                    $page->seo_title = 'Travel & Agen Umroh Jogja: Harga, Jadwal & Konsultasi';
                }
                $page->save();
                SiteSetting::create(['key' => $marker, 'value' => '1']);
            }
        });
    }
}
