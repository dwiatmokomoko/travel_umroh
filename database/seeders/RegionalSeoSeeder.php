<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionalSeoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $page = Page::where('slug', 'umrah-jogja')->first();
            if (! $page || SiteSetting::where('key', 'seed_regional_seo_v1')->exists()) {
                return;
            }
            $page->body .= <<<'MD'


## Mencari umrah atau umroh termurah di Jogja?
Mulailah dengan membandingkan total biaya dan fasilitas untuk tanggal serta durasi yang sama. Harga tiket, hotel, pembagian kamar, visa, bagasi, dan transportasi penghubung dapat membuat penawaran berbeda. Pilih paket yang sesuai anggaran setelah mengetahui biaya yang termasuk dan belum termasuk.

Gunakan [urutan harga terendah pada katalog Safir](/paket-umrah?sort=price) untuk mencari pilihan paling hemat di antara paket Safir yang sedang dipublikasikan. Harga terendah di katalog Safir bukan klaim bahwa Safir paling murah dibanding seluruh travel di Jogja atau Indonesia. Mintalah rincian harga akhir sebelum membayar.

## Memilih travel umroh terpercaya di Indonesia dan Jogja
Kepercayaan perlu didukung informasi yang bisa diperiksa: identitas badan usaha, dokumen izin PPIU, alamat kantor, rincian paket tertulis, kebijakan pembayaran, serta pengalaman jamaah asli. Pelajari [profil dan legalitas Safir](/tentang-kami), minta konfirmasi dokumen terbaru, dan baca [testimoni jamaah yang telah mengizinkan publikasi](/testimoni).

Kontak utama Safir Jogja adalah {contact}, melalui {phone}. Tim juga melayani konsultasi jarak jauh bagi calon jamaah seluruh Indonesia. Konfirmasikan rekening resmi dan perjanjian perjalanan langsung kepada tim; situs ini tidak menerima pembayaran.

## Umroh pasti berangkat sesuai jadwal: apa yang perlu dikonfirmasi?
Sebelum memilih program berdasarkan janji “pasti berangkat”, mintalah konfirmasi tertulis mengenai tanggal, maskapai, bandara, tiket, visa, hotel, dan ketentuan perubahan atau pembatalan. Jadwal yang ditampilkan di situs adalah rencana paket yang perlu dikonfirmasi, bukan jaminan tanpa syarat bahwa perjalanan tidak mungkin berubah.

Periksa [jadwal paket umrah](/paket-umrah), lalu sampaikan jumlah peserta dan tanggal pilihan kepada tim. Jadwal manasik, titik temu, serta perjalanan dari daerah asal perlu disepakati sebelum berangkat.

## Layanan konsultasi umrah di seluruh kabupaten dan kecamatan DIY
Konsultasi tersedia bagi calon jamaah dari seluruh Daerah Istimewa Yogyakarta melalui WhatsApp atau kunjungan dengan janji ke kantor Bantul di {address}. Wilayah berikut merupakan cakupan layanan konsultasi, bukan daftar kantor cabang atau titik penjemputan. Transportasi penghubung, manasik, dan titik keberangkatan dikonfirmasi sesuai paket serta domisili jamaah.
MD;
            foreach (config('service-areas') as $region => $area) {
                $page->body .= "\n\n### ".$region."\n".implode(', ', $area['districts']).".\n\n[Rujukan nama wilayah: ".$region.']('.$area['source'].').';
            }
            $page->body .= "\n\nSampaikan kecamatan asal, jumlah jamaah, anggaran, dan pilihan waktu melalui [konsultasi dengan Wisnu, Ryan, atau Dwi](/kontak#konsultan) agar tim dapat membantu membandingkan paket.";
            if (in_array($page->seo_title, ['Travel Umroh Jogja: Konsultasi Umrah di Bantul', 'Travel Umrah Jogja, Bantul & seluruh Indonesia'])) {
                $page->seo_title = 'Travel Umroh Jogja: Harga, Jadwal & Layanan Seluruh DIY';
            }
            if ($page->seo_description === 'Konsultasi umrah untuk Yogyakarta, Bantul, Sleman, Kulon Progo, dan Gunungkidul.') {
                $page->seo_description = 'Mencari umroh termurah dan terpercaya di Jogja? Bandingkan harga, cek legalitas dan jadwal Safir. Konsultasi untuk seluruh DIY dan Indonesia.';
            }
            $page->save();
            SiteSetting::create(['key' => 'seed_regional_seo_v1', 'value' => '1']);
        });
    }
}
