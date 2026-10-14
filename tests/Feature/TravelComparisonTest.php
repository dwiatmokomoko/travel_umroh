<?php

namespace Tests\Feature;

use App\Models\Page;
use Database\Seeders\TravelComparisonSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TravelComparisonTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_comparison_guide_is_visible_and_discoverable_without_claiming_affiliation(): void
    {
        $this->seed(TravelComparisonSeeder::class);
        $response = $this->get('/perbandingan-travel-umroh-jogja')->assertOk();
        foreach (['Dewangga Jogja', 'Nur Ramadhan', 'Batik Umrah', 'Hasuna Tour', 'Zhafirah Travel', 'Faraya Tours'] as $name) {
            $response->assertSee($name);
        }
        $response->assertSee('bukan pemeringkatan independen')->assertSee('tidak menyatakan hubungan afiliasi')
            ->assertSee('href="https://umrahjogja.com/"', false)->assertSee('href="/paket-umrah"', false)
            ->assertSee('name="description"', false);
        $this->get('/sitemap.xml')->assertOk()->assertSee('/perbandingan-travel-umroh-jogja');
        $this->get('/')->assertOk()->assertSee('/perbandingan-travel-umroh-jogja');
    }

    public function test_reseeding_preserves_admin_edits_and_hidden_status(): void
    {
        $this->seed(TravelComparisonSeeder::class);
        $page = Page::where('slug', 'perbandingan-travel-umroh-jogja')->firstOrFail();
        $page->update(['body' => 'Panduan yang diperbarui admin.', 'is_published' => false]);
        $this->seed(TravelComparisonSeeder::class);
        $this->assertDatabaseCount('pages', 1);
        $this->assertSame('Panduan yang diperbarui admin.', $page->fresh()->body);
        $this->get('/perbandingan-travel-umroh-jogja')->assertNotFound();
        $this->get('/sitemap.xml')->assertDontSee('/perbandingan-travel-umroh-jogja');
    }
}
