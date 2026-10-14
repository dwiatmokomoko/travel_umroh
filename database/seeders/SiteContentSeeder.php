<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class SiteContentSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            'tentang-kami' => ['Tentang Safir Caturjaya Wisata', 'Mendampingi perjalanan ibadah dan wisata, hadir lebih dekat untuk calon jamaah di Yogyakarta.', <<<'MD'
## Berawal dari pengalaman, bertumbuh dalam pelayanan
Menurut profil perusahaan 2025, PT Safir Caturjaya Wisata didirikan pada Maret 2022 di Tangerang, Banten, dan kemudian berkantor pusat di Karawang, Jawa Barat. Para pendirinya memiliki pengalaman bekerja sama dalam layanan tiket umrah sejak 2012.

Di Jogja, calon jamaah dapat berkonsultasi melalui kantor Bantul. Konsultasi jarak jauh tersedia bagi calon jamaah seluruh Indonesia.

## Layanan kami
- Perjalanan ibadah umrah dengan pendampingan persiapan dan bimbingan ibadah.
- Konsultasi tiket pesawat domestik dan internasional.
- Perjalanan wisata domestik, internasional, dan halal tour.
- Pendampingan dokumen perjalanan serta kegiatan meeting, incentive, convention, dan exhibition (MICE).

## Informasi legalitas
**{company}** — Nomor izin PPIU **{license}**, NIB 1103220030433. Dokumen izin diterbitkan pada 31 Oktober 2023.

Alamat pada izin: Dusun Karajan, Jl. Raya Desa Pangulah Selatan, Kotabaru, Karawang, Jawa Barat 41376. Alamat ini berbeda dari kantor pelayanan Jogja. Informasi mengacu pada dokumen perusahaan; hubungi tim untuk pemeriksaan dokumen dan status terbaru.
MD],
            'umrah-jogja' => ['Travel Umrah Jogja, Bantul & seluruh Indonesia', 'Konsultasi umrah untuk Yogyakarta, Bantul, Sleman, Kulon Progo, dan Gunungkidul.', <<<'MD'
## Merencanakan umrah dari Yogyakarta
Mulai dari memilih jadwal, memahami biaya, hingga menyiapkan perjalanan dari rumah, tim Safir Jogja membantu Anda menyusun langkah keberangkatan. Konsultasi tersedia secara langsung maupun melalui WhatsApp.

## Kantor Bantul
{address}

Silakan membuat janji dengan {contact} melalui {phone} sebelum berkunjung.

## Konfirmasi sebelum berangkat
- **Titik keberangkatan:** pelayanan di Jogja tidak berarti semua penerbangan berangkat dari YIA. Pastikan bandara dan rute paket.
- **Transportasi daerah:** tanyakan cakupan akomodasi daerah dan biaya penghubung dari rumah.
- **Manasik:** pastikan lokasi dan jadwal sebelum keberangkatan.
- **Dokumen:** paspor dan vaksin belum termasuk harga brosur.

## Untuk jamaah seluruh Indonesia
Sampaikan domisili dan jumlah peserta agar tim dapat mengecek perjalanan penghubung, titik temu rombongan, serta proses administrasi.
MD],
            'kontak' => ['Hubungi Safir Jogja', 'Ceritakan rencana perjalanan Anda. Kami membantu memilih paket dan mempersiapkan keberangkatan.', 'Silakan membuat janji sebelum berkunjung. Konsultasi bersama {contact} melalui {phone}.'],
            'panduan-umrah' => ['Panduan Persiapan Umrah', 'Persiapan yang baik membantu Anda menjalani perjalanan ibadah dengan lebih tenang.', <<<'MD'
## 1. Tentukan waktu dan kebutuhan perjalanan
Sesuaikan rencana dengan waktu luang, jumlah peserta, dan kebutuhan pendampingan keluarga. Tinjau jadwal di halaman paket untuk pilihan terbaru.

## 2. Bandingkan isi paket
Periksa maskapai, hotel Makkah dan Madinah, durasi, serta fasilitas. Tanyakan pembagian kamar, bandara keberangkatan, bagasi, dan itinerary. Minta kesepakatan tertulis.

## 3. Siapkan dokumen bersama tim
Persyaratan administrasi dan kesehatan dapat berubah; konfirmasikan ketentuan terbaru sebelum mengurus dokumen. Paspor dan vaksin tercantum sebagai biaya di luar paket dalam brosur.

## 4. Konfirmasikan pembayaran
Brosur mencantumkan DP Rp2 juta. Sebelum membayar, konfirmasikan ketersediaan, harga akhir, jadwal pelunasan, kebijakan pembatalan, serta rekening resmi. Situs ini tidak menerima pembayaran.

## 5. Ikuti manasik
Pastikan waktu manasik, perlengkapan, titik temu, dan nomor pendamping. Jamaah luar kota sebaiknya menyusun transportasi penghubung setelah jadwal penerbangan dikonfirmasi.
MD],
            'privasi' => ['Kebijakan Privasi', 'Informasi pemrosesan data dan penggunaan layanan pihak ketiga.', <<<'MD'
## Data konsultasi
Formulir konsultasi diproses di browser dan tidak dikirim ke database situs. Tombol WhatsApp menyiapkan draf pesan. Pesan terkirim setelah Anda mengirimkannya melalui WhatsApp.

## Tautan pihak ketiga
WhatsApp, Google Maps, dan sumber berita memiliki kebijakan privasi masing-masing ketika Anda membuka tautannya.

## Cookie dan catatan teknis
Situs menggunakan cookie sesi untuk fungsi teknis dan autentikasi administrator. Server dapat mencatat alamat IP, waktu akses, serta kesalahan aplikasi untuk operasional. Tidak ada pelacak iklan pihak ketiga yang dipasang.

## Testimoni
Nama, kota, foto, dan cerita jamaah ditampilkan setelah perusahaan mengonfirmasi izin publikasi. Hubungi {contact} melalui {phone} untuk permintaan koreksi atau penghapusan.

## Kontak
Hindari memasukkan nomor paspor, data kesehatan, atau informasi pembayaran ke formulir konsultasi umum. Pertanyaan penggunaan data dapat disampaikan melalui {phone}.
MD],
        ];
        foreach ($pages as $slug => [$title, $intro, $body]) {
            Page::firstOrCreate(['slug' => $slug], ['title' => $title, 'intro' => $intro, 'body' => $body, 'seo_title' => $title, 'seo_description' => $intro, 'is_published' => true]);
        }
    }
}
