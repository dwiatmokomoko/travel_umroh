<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UmrahPlanningSeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $page = Page::where('slug', 'panduan-umrah')->first();
            $marker = 'seed_umrah_planning_v1';
            if (! $page || SiteSetting::where('key', $marker)->exists()) {
                return;
            }
            $page->body .= "\n\n".<<<'MD'
## Merencanakan umroh dari Jogja: kamar, penerbangan, dan kebutuhan keluarga

### Paket umroh quad, triple, dan double: apa yang perlu dibandingkan?
Minta penawaran berdasarkan jumlah penghuni kamar yang sama. Tanyakan konfigurasi tempat tidur, kemungkinan berbagi kamar dengan jamaah lain, dan tambahan biaya bila jumlah peserta berubah. Cocokkan nama hotel Makkah dan Madinah serta jumlah malam dengan itinerary tertulis.

### Umroh 9 hari atau 12 hari: bagaimana memilih durasi?
Periksa hari penerbangan, malam menginap, kegiatan di Makkah dan Madinah, serta waktu istirahat. Jumlah hari program tidak selalu sama dengan jumlah malam di hotel. Bandingkan [durasi dan fasilitas paket](/paket-umrah) bersama kebutuhan keluarga dan waktu cuti.

### Berangkat dari YIA atau bandara lain?
Konsultasi dengan travel di Jogja tidak otomatis berarti penerbangan berangkat dari Yogyakarta International Airport. Konfirmasikan bandara keberangkatan dan kepulangan, titik kumpul, perjalanan penghubung, serta biayanya. Untuk penerbangan transit, tanyakan durasi transit dan penanganan bagasi.

### Garuda Indonesia atau Lion Air: informasi apa yang harus dicek?
Gunakan maskapai yang tercantum pada paket pilihan sebagai awal diskusi. Minta konfirmasi penerbangan, rute, jatah bagasi, layanan makan, dan ketentuan perubahan jadwal. Logo maskapai di situs tidak berarti semua paket memakai maskapai tersebut atau memiliki penerbangan langsung.

### Bagaimana memilih hotel dekat Masjidil Haram dan Masjid Nabawi?
Tanyakan nama hotel, rute berjalan menuju akses masjid, layanan antar-jemput jika tersedia, serta jenis kamar. Pertimbangkan kebutuhan mobilitas anggota keluarga dan waktu istirahat. Pastikan ketentuan hotel pengganti dijelaskan dalam penawaran.

### Apa yang perlu disiapkan untuk umrah bersama anak dan orang tua?
Sampaikan jumlah peserta, usia, kebutuhan kamar, dan bantuan mobilitas saat konsultasi. Tanyakan ketersediaan pendampingan, makanan, waktu istirahat, dan biaya tambahan. Layanan khusus perlu dikonfirmasi sebelum pembayaran.

### Bagaimana mencari paket umroh hemat?
Bandingkan [harga paket umrah](/paket-umrah?sort=price) untuk tanggal, durasi, dan fasilitas setara. Minta daftar biaya yang termasuk dan tidak termasuk, terutama transportasi penghubung, dokumen, perubahan kamar, serta kebutuhan pribadi. Harga terendah dalam katalog Safir bukan pernyataan termurah di seluruh Jogja.

### Bagaimana memastikan jadwal sebelum melunasi paket?
Minta itinerary terbaru, status konfirmasi tiket dan hotel, batas pelunasan, serta ketentuan bila jadwal berubah. Simpan penawaran dan bukti pembayaran. Tanyakan prosedur pembatalan dan pengembalian dana sebelum menyetujui pemesanan.

### Apakah jamaah dari luar Jogja dapat berkonsultasi?
Konsultasi tersedia melalui [Wisnu, Ryan, dan Dwi](/kontak#konsultan). Sampaikan kota asal agar tim dapat menjelaskan titik temu dan perjalanan penghubung. Untuk warga Bantul, Sleman, Kota Yogyakarta, Kulon Progo, dan Gunungkidul, lihat [cakupan layanan Safir di DIY](/umrah-jogja).

### Informasi apa yang sebaiknya dikirim saat meminta penawaran?
Siapkan bulan keberangkatan, jumlah jamaah, kota asal, anggaran, durasi, serta pilihan kamar. Tim dapat membantu mencocokkan kebutuhan dengan [paket dan jadwal yang dipublikasikan](/paket-umrah). Baca juga [panduan membandingkan travel umroh Jogja](/perbandingan-travel-umroh-jogja) sebelum menentukan pilihan.
MD;
            $page->save();
            SiteSetting::create(['key' => $marker, 'value' => '1']);
        });
    }
}
