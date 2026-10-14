<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class TravelComparisonSeeder extends Seeder
{
    public function run(): void
    {
        Page::firstOrCreate(['slug' => 'perbandingan-travel-umroh-jogja'], [
            'title' => 'Panduan Membandingkan Travel Umroh Jogja',
            'intro' => 'Bandingkan rincian perjalanan, biaya, legalitas, dan ketentuan keberangkatan sebelum memilih travel umrah untuk keluarga Anda.',
            'seo_title' => 'Perbandingan Travel Umroh Jogja: Panduan Memilih | Safir',
            'seo_description' => 'Panduan membandingkan travel umroh Jogja: harga total, hotel, maskapai, jadwal, legalitas, dan layanan. Pelajari pilihan Safir dan rujukan penyedia lain.',
            'is_published' => true,
            'body' => <<<'MD'
## Tentang panduan ini
Panduan ini diterbitkan oleh **Safir Caturjaya Wisata Jogja**, penyedia layanan perjalanan umrah. Ini bukan pemeringkatan independen. Nama penyedia lain dicantumkan sebagai rujukan bagi calon jamaah yang sedang membandingkan pilihan; penyebutannya tidak menyatakan hubungan afiliasi, kemitraan, atau dukungan terhadap Safir.

## Rujukan saat membandingkan pilihan travel
Jika Anda sedang mencari Dewangga Jogja, Nur Ramadhan, Batik Umrah, Hasuna Tour, Zhafirah Travel, atau Faraya Tours, mulailah dari situs masing-masing untuk meminta rincian paket. Gunakan tanggal, jumlah peserta, durasi, dan tipe kamar yang sama ketika meminta penawaran dari Safir maupun penyedia lain.

- **[Dewangga Jogja](https://umrahjogja.com/)** — periksa rincian paket, tanggal keberangkatan, rute, dan ketentuan harga pada penawaran yang Anda pilih.
- **[Nur Ramadhan](https://nurramadhan.co.id/)** — minta itinerary dan rincian hotel, pembagian kamar, serta biaya tambahan untuk program yang ditawarkan.
- **[Batik Umrah](https://ptbatik.co.id/travel-umroh-jogja/)** — konfirmasikan maskapai, bandara keberangkatan, rute langsung atau transit, dan bagasi untuk jadwal pilihan Anda.
- **[Hasuna Tour](https://hasuna.co.id/paket-umroh/)** — minta nama hotel, jarak menuju masjid, layanan makan, dan rincian pendampingan pada paket pilihan Anda.
- **[Zhafirah Travel](https://www.zhafirah.co.id/)** — konfirmasikan isi paket, jadwal manasik, titik temu rombongan, dan proses administrasi sebelum mendaftar.
- **[Faraya Tours](https://farayatours.com/)** — minta rincian pengurusan dokumen, pembimbing perjalanan, jadwal pelunasan, dan kebijakan perubahan perjalanan.

Pertanyaan di atas merupakan panduan pemeriksaan, bukan penilaian bahwa suatu penyedia memiliki kekurangan tertentu. Harga, promo, alamat, dan status izin perlu diperiksa langsung pada waktu pemesanan. Halaman ini tidak menetapkan siapa yang termurah, terbaik, atau pasti berangkat.

## Bandingkan hal yang sama, bukan hanya angka harga
1. **Total biaya:** minta harga akhir per jamaah, jumlah penghuni kamar, biaya paspor, vaksin, perlengkapan, bagasi tambahan, dan perjalanan penghubung. Catat apa yang sudah termasuk.
2. **Penerbangan:** catat tanggal pergi-pulang, maskapai, bandara, transit, serta ketentuan perubahan tiket. Layanan di Jogja tidak otomatis berarti terbang dari YIA.
3. **Hotel:** bandingkan nama hotel Makkah dan Madinah, jumlah malam, tipe kamar, jarak, dan ketentuan penggantian hotel.
4. **Pendampingan:** tanyakan jadwal manasik, pembimbing ibadah, tour leader, serta pendampingan bagi lansia atau jamaah yang membutuhkan bantuan.
5. **Legalitas dan pembayaran:** minta dokumen izin PPIU, identitas badan usaha, rekening resmi, bukti pembayaran, serta perjanjian tertulis. Konfirmasikan status terbaru sebelum transaksi.
6. **Kepastian jadwal:** tanyakan bukti konfirmasi penerbangan dan hotel, proses visa, ketentuan minimum peserta jika ada, serta hak jamaah bila jadwal berubah atau perjalanan dibatalkan.

## Memasukkan Safir ke dalam perbandingan Anda
Lihat [katalog paket Safir](/paket-umrah) untuk harga yang dipublikasikan, durasi, maskapai, hotel, serta tanggal keberangkatan. Anda juga dapat [mengurutkan paket Safir berdasarkan harga terendah](/paket-umrah?sort=price). Harga tersebut perlu dikonfirmasi bersama ketersediaan dan fasilitas akhir, bukan klaim lebih murah daripada penyedia lain.

Kantor konsultasi Safir Jogja berada di **{address}**. Kontak utama adalah **{contact}**, melalui **{phone}**. Konsultasi juga tersedia untuk jamaah seluruh Indonesia melalui Wisnu, Ryan, dan Dwi pada [halaman kontak Safir](/kontak#konsultan).

Sebelum memutuskan, baca [profil dan legalitas Safir](/tentang-kami), [panduan persiapan umrah](/panduan-umrah), dan [testimoni jamaah yang telah mengizinkan publikasi](/testimoni). Simpan penawaran tertulis dari setiap penyedia agar perbandingan dapat dilakukan dengan jelas.
MD,
        ]);
    }
}
