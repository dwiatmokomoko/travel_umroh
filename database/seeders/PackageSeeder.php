<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departures = [
            ['2026-10-15', 9, false, 33000000, 'Lion Air', 'Arguan Al Salam', 3],
            ['2026-10-18', 12, true, 35000000, 'Lion Air', 'Arguan Al Salam', 3],
            ['2026-10-25', 9, false, 34000000, 'Garuda Indonesia', 'Abraj Tabah', 4],
            ['2026-11-12', 9, false, 32000000, 'Lion Air', 'Arguan Al Salam', 3],
            ['2026-11-15', 12, true, 35000000, 'Lion Air', 'Arguan Al Salam', 3],
            ['2026-11-28', 9, true, 34000000, 'Garuda Indonesia', 'Abraj Tabah', 4],
            ['2026-11-29', 12, true, 35000000, 'Lion Air', 'Arguan Al Salam', 3],
            ['2026-12-03', 9, true, 34000000, 'Saudia', 'Abraj Tabah', 4],
            ['2026-12-03', 9, false, 32000000, 'Lion Air', 'Arguan Al Salam', 3],
            ['2026-12-06', 12, true, 35000000, 'Lion Air', 'Arguan Al Salam', 3],
            ['2026-12-16', 9, true, 34000000, 'Saudia', 'Abraj Tabah', 4],
        ];
        $facilities = <<<'MD'
## Termasuk dalam brosur
- Tiket pesawat pulang-pergi (direct flight).
- Hotel Makkah dan Madinah sesuai pilihan paket.
- Bus exclusive, handling bandara, dan akomodasi daerah.
- Manasik, mutawif, dan tour leader.
- Makan 3 kali sehari.
- Pengurusan visa, asuransi, dan perlengkapan.
- Air Zamzam 5 liter.
- Layanan Raudhah (brosur mencantumkan "Jaminan Raudhah"); konfirmasikan jadwal dan ketentuan kepada tim.

## Belum termasuk
Paspor dan vaksin.

## Pendaftaran
Brosur mencantumkan DP Rp2 juta. Konfirmasikan ketersediaan, harga akhir, bandara, pembagian kamar, cakupan akomodasi daerah, serta syarat pembayaran sebelum mendaftar.

Sumber: brosur paket umrah Safir Oktober-Desember 2026, jadwal Oktober–Desember 2026.
MD;
        foreach ($departures as [$date, $days, $thaif, $price, $airline, $hotel, $stars]) {
            $name = 'Umrah '.$days.' Hari'.($thaif ? ' + Thaif' : '');
            $details = [
                'description' => $name.' bersama Safir Caturjaya Wisata, keberangkatan '.$date.'. Informasi mengacu pada brosur Oktober–Desember 2026; jadwal dan fasilitas perlu dikonfirmasi sebelum pemesanan.',
                'facilities' => $facilities,
                'seo_title' => $name.' '.$date.' | Safir Jogja',
                'seo_description' => $name.' keberangkatan '.$date.' dengan '.$airline.'. Harga brosur Rp'.number_format($price, 0, ',', '.').'. Konsultasi Safir Jogja untuk jamaah seluruh Indonesia.',
            ];
            $package = Package::firstOrCreate(['slug' => 'umrah-'.$days.'-hari-'.($thaif ? 'thaif-' : '').$date], [
                'name' => 'Umrah '.$days.' Hari'.($thaif ? ' + Thaif' : ''),
                'departure_date' => $date, 'days' => $days, 'includes_thaif' => $thaif,
                'price' => $price, 'airline' => $airline, 'madinah_hotel' => $hotel,
                'madinah_stars' => $stars, 'makkah_hotel' => 'Grand Al Masya', 'makkah_stars' => 4,
                'is_published' => true,
                ...$details,
            ]);

            // Backfill fields missing from the original import, preserving edits,
            // including intentionally cleared text and publication decisions.
            $actualDate = $package->departure_date->format('Y-m-d');
            $details['description'] = $package->name.' bersama Safir Caturjaya Wisata, keberangkatan '.$actualDate.'. Informasi awal mengacu pada brosur Oktober–Desember 2026; konfirmasikan jadwal dan fasilitas sebelum pemesanan.';
            $details['seo_title'] = $package->name.' '.$actualDate.' | Safir Jogja';
            $details['seo_description'] = $package->name.' keberangkatan '.$actualDate.' dengan '.$package->airline.'. Harga Rp'.number_format($package->price, 0, ',', '.').'. Konsultasi Safir Jogja untuk jamaah seluruh Indonesia.';
            foreach ($details as $field => $value) {
                if ($package->getAttribute($field) === null) {
                    $package->setAttribute($field, $value);
                }
            }
            if ($package->isDirty()) {
                $package->save();
            }
        }
    }
}
