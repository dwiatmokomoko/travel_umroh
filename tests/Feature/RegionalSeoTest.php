<?php

namespace Tests\Feature;

use App\Models\Page;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\RegionalSeoSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class RegionalSeoTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_all_diy_districts_are_visible_as_service_areas_without_restoring_old_office(): void
    {
        $this->seed(DatabaseSeeder::class);
        $response = $this->get('/umrah-jogja')->assertOk();
        $expectedCounts = ['Kabupaten Bantul' => 17, 'Kabupaten Sleman' => 17, 'Kabupaten Gunungkidul' => 18, 'Kabupaten Kulon Progo' => 12, 'Kota Yogyakarta' => 14];
        foreach ($expectedCounts as $region => $count) {
            $districts = config('service-areas.'.$region.'.districts');
            $this->assertCount($count, $districts);
            $this->assertCount($count, array_unique($districts));
            $response->assertSee($region);
            foreach ($districts as $district) {
                $response->assertSee($district);
            }
        }
        $response->assertSee('bukan daftar kantor cabang')->assertDontSee('Tentara Pelajar')->assertDontSee('Happy Kids Educare')
            ->assertSee('umroh termurah')->assertSee('travel umroh terpercaya')->assertSee('Umroh pasti berangkat sesuai jadwal')
            ->assertSee('bukan jaminan tanpa syarat')->assertSee('bukan klaim bahwa Safir paling murah');
        preg_match_all('/<script type="application\/ld\+json">(.*?)<\/script>/s', $response->getContent(), $matches);
        $schemas = collect($matches[1])->map(fn ($json) => json_decode($json, true, 512, JSON_THROW_ON_ERROR));
        $service = $schemas->firstWhere('@type', 'Service');
        $this->assertCount(6, $service['areaServed']);
        $this->assertSame(route('local'), $service['url']);
    }

    public function test_regional_seed_preserves_custom_metadata_and_later_editorial_changes(): void
    {
        $page = Page::factory()->create(['slug' => 'umrah-jogja', 'body' => 'Konten admin.', 'seo_title' => 'Judul pilihan admin', 'seo_description' => 'Deskripsi pilihan admin']);
        $this->seed(RegionalSeoSeeder::class);
        $this->assertStringStartsWith('Konten admin.', $page->fresh()->body);
        $this->assertSame('Judul pilihan admin', $page->fresh()->seo_title);
        $this->assertSame('Deskripsi pilihan admin', $page->fresh()->seo_description);
        $page->refresh()->update(['body' => 'Konten revisi admin.']);
        $this->seed(RegionalSeoSeeder::class);
        $this->assertSame('Konten revisi admin.', $page->fresh()->body);
    }
}
