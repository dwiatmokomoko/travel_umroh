<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\Page;
use Database\Seeders\PageSeeder;
use Database\Seeders\TrustSeoSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class MonthlyPlanningSeoTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_calendar_spans_sixty_months_and_counts_only_public_upcoming_packages(): void
    {
        $this->travelTo(now('Asia/Jakarta')->setDate(2026, 10, 17));
        Package::factory()->create(['departure_date' => '2026-11-10', 'is_published' => true]);
        Package::factory()->create(['departure_date' => '2026-11-11', 'is_published' => false]);
        $this->get('/paket-umrah')->assertOk()->assertSee('September 2031')->assertSee('Jadwal belum dipublikasikan')
            ->assertViewHas('planningMonths', function ($months): bool {
                return $months->count() === 60 && $months->first()['key'] === '2026-10'
                    && $months->last()['key'] === '2031-09' && $months->get(1)['count'] === 1;
            });
        $this->get('/paket-umrah?month=2031-09')->assertOk()->assertViewHas('noindex', true);
        $this->travelTo(now('Asia/Jakarta')->setDate(2026, 11, 1));
        $this->get('/paket-umrah')->assertOk()->assertViewHas('planningMonths', fn ($months) => $months->first()['key'] === '2026-11' && $months->last()['key'] === '2031-10');
    }

    public function test_trust_content_is_published_once_and_admin_edits_survive(): void
    {
        $this->seed(PageSeeder::class);
        $this->seed(TrustSeoSeeder::class);
        $this->get('/')->assertOk()->assertSee('aman dan amanah');
        $this->get('/umrah-jogja')->assertOk()->assertSee('Memilih agen, travel, atau biro umroh');
        $page = Page::where('slug', 'umrah-jogja')->firstOrFail();
        $body = $page->body;
        $this->seed(TrustSeoSeeder::class);
        $this->assertSame($body, $page->fresh()->body);
        $page->update(['body' => 'Konten admin']);
        $this->seed(TrustSeoSeeder::class);
        $this->assertSame('Konten admin', $page->fresh()->body);
    }
}
