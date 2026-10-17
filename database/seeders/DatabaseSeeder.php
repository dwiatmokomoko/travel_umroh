<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $this->call([
                OfficeAddressCorrectionSeeder::class,
                PrimaryContactSeeder::class,
                SiteSettingSeeder::class,
                PackageSeeder::class,
                PageSeeder::class,
                LocalSeoSeeder::class,
                RegionalSeoSeeder::class,
                TravelComparisonSeeder::class,
                UmrahTopicsSeoSeeder::class,
                UmrahPlanningSeoSeeder::class,
                TrustSeoSeeder::class,
                ArticleSeeder::class,
                TestimonialSeeder::class,
                AdminSeeder::class,
            ]);
        });
    }
}
