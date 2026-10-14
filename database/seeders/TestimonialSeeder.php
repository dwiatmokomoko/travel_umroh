<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            'seed_testimonial_template' => ['[CONTOH] Template testimoni jamaah', 'Isi kota jamaah', 'Ganti dengan pengalaman asli jamaah selama perjalanan umrah.'],
            'seed_testimonial_family' => ['[CONTOH] Testimoni perjalanan keluarga', 'Isi kota keluarga', 'Ganti dengan cerita asli jamaah tentang perjalanan bersama keluarga dan pendampingan yang diterima.'],
            'seed_testimonial_preparation' => ['[CONTOH] Testimoni persiapan keberangkatan', 'Isi kota jamaah', 'Ganti dengan pengalaman asli jamaah saat konsultasi paket, persiapan dokumen, dan manasik.'],
        ];
        foreach ($templates as $key => [$name, $city, $body]) {
            if (SiteSetting::where('key', $key)->exists()) {
                continue;
            }
            $testimonial = Testimonial::firstOrCreate(['name' => $name], [
                'city' => $city, 'rating' => 5,
                'body' => 'CONTOH FORMAT, BUKAN TESTIMONI NYATA. '.$body.' Sesuaikan nama, kota, rating, dan tanggal perjalanan. Konfirmasikan izin publikasi sebelum menerbitkan.',
                'is_published' => false, 'consent_confirmed' => false,
            ]);
            SiteSetting::create(['key' => $key, 'value' => (string) $testimonial->id]);
        }
    }
}
