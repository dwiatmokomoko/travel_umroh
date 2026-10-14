<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Package;
use App\Models\Page;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrimaryContactSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $replacements = [
                'A5 DAVID GK_3.pdf' => 'brosur paket umrah Safir Oktober–Desember 2026',
                'David' => 'Wisnu',
                '0813-5003-5003' => '0821-3604-2015',
                '0813.5003.5003' => '0821-3604-2015',
                '081350035003' => '082136042015',
                '6281350035003' => '6282136042015',
            ];
            foreach ([
                SiteSetting::class => ['value'],
                Page::class => ['title', 'intro', 'body', 'seo_title', 'seo_description'],
                Package::class => ['description', 'facilities', 'seo_title', 'seo_description'],
                Article::class => ['title', 'excerpt', 'body', 'seo_title', 'seo_description'],
            ] as $model => $fields) {
                foreach ($model::all() as $record) {
                    foreach ($fields as $field) {
                        if ($record->$field !== null) {
                            $record->$field = str_replace(array_keys($replacements), array_values($replacements), $record->$field);
                        }
                    }
                    if ($record->isDirty()) {
                        $record->save();
                    }
                }
            }
        });
    }
}
