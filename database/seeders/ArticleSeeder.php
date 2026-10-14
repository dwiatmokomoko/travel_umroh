<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            ['mengenal-layanan-safir-jogja', 'Mengenal Layanan Safir Caturjaya Wisata Jogja', 'Kenali layanan perjalanan Safir dan konsultasi umrah dari Yogyakarta untuk calon jamaah seluruh Indonesia.', "## Layanan perjalanan\nProfil perusahaan Safir 2025 memuat layanan tiket pesawat domestik dan internasional, perjalanan umrah, paket wisata, pengurusan dokumen perjalanan, dan MICE.\n\n## Konsultasi dari Jogja\nTim Wisnu, Ryan, dan Dwi membantu calon jamaah melalui pilihan WhatsApp di halaman kontak. Konsultasi langsung tersedia di kantor Bantul dengan membuat janji terlebih dahulu.\n\nSumber penyusunan: COMPRO SAFIR 2025_compressed.pdf dan informasi kontak pengelola situs. Tinjau kembali sebelum publikasi."],
            ['memahami-fasilitas-paket-umrah-safir', 'Memahami Fasilitas Paket Umrah Safir', 'Pelajari fasilitas paket umrah dan hal yang perlu dikonfirmasi kepada tim sebelum menentukan keberangkatan.', "## Membaca fasilitas paket\nBrosur Safir Oktober–Desember 2026 mencantumkan tiket pulang-pergi, hotel, bus, manasik, pendamping, makan tiga kali sehari, visa, asuransi, perlengkapan, dan Zamzam 5 liter. Paspor dan vaksin tercantum di luar paket.\n\n## Konfirmasi sebelum mendaftar\nTanyakan ketersediaan, harga akhir, bandara keberangkatan, pembagian kamar, jadwal Raudhah, serta cakupan akomodasi daerah untuk paket pilihan Anda. Brosur mencantumkan DP Rp2 juta; ketentuan pembayaran harus dikonfirmasi kepada tim.\n\nSumber penyusunan: brosur paket umrah Safir Oktober-Desember 2026. Ini ringkasan brosur, bukan pembaruan regulasi. Tinjau kembali sebelum publikasi."],
        ];
        foreach ($articles as [$slug, $title, $excerpt, $body]) {
            Article::firstOrCreate(['slug' => $slug], [
                'title' => $title, 'excerpt' => $excerpt, 'body' => $body,
                'author' => 'Redaksi Safir Jogja', 'seo_title' => $title,
                'seo_description' => $excerpt, 'is_published' => false, 'published_at' => null,
            ]);
        }
    }
}
