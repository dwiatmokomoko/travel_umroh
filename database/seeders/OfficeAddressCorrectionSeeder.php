<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class OfficeAddressCorrectionSeeder extends Seeder
{
    public function run(): void
    {
        SiteSetting::where('key', 'business_gunungkidul')->delete();
        SiteSetting::where('key', 'copy10_011')->where('value', 'GUNUNGKIDUL')->update(['value' => 'KONSULTASI ONLINE']);
        $replacements = [
            "## Kantor pelayanan Gunungkidul\n{gunungkidul}\n\n" => '',
            'Jl. Tentara Pelajar No. 9A, Trimulyo I, Kepek, Wonosari, Gunungkidul (Happy Kids Educare)' => '',
            '{gunungkidul}' => '',
            'kantor Bantul dan kantor pelayanan Gunungkidul' => 'kantor Bantul',
            'Bantul atau Gunungkidul' => 'Bantul',
            'Bantul dan Gunungkidul' => 'Bantul',
            'Kantor pelayanan Gunungkidul ↗' => 'Hubungi tim konsultasi ↗',
            '02 / GUNUNGKIDUL' => '02 / KONSULTASI ONLINE',
            'Lebih dekat dari Wonosari.' => 'Terhubung dari seluruh Indonesia.',
            'Bantul & Gunungkidul, Yogyakarta' => 'Bantul, Yogyakarta',
            'Bantul & Gunungkidul' => 'Bantul & seluruh Indonesia',
            'Pelayanan Gunungkidul' => 'Konsultasi online',
            'Kantor pelayanan Wonosari' => 'Konsultasi online',
        ];
        foreach (SiteSetting::all() as $setting) {
            $setting->value = str_replace(array_keys($replacements), array_values($replacements), $setting->value ?? '');
            if ($setting->isDirty()) {
                $setting->save();
            }
        }
        foreach (Page::all() as $page) {
            foreach (['title', 'intro', 'body', 'seo_title', 'seo_description'] as $field) {
                if ($page->$field !== null) {
                    $page->$field = str_replace(array_keys($replacements), array_values($replacements), str_replace("\r\n", "\n", $page->$field));
                }
            }
            if ($page->isDirty()) {
                $page->save();
            }
        }
    }
}
