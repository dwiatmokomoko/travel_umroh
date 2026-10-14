<?php

namespace Tests\Feature;

use App\Models\Package;
use Carbon\Carbon;
use Database\Seeders\PackageSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class WebsiteTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_home_shows_real_upcoming_packages_and_business_metadata(): void
    {
        $this->travelTo(Carbon::parse('2026-10-10 12:00:00'));
        $this->seed(PackageSeeder::class);

        $this->get('/')->assertOk()->assertSee('Mulai Rp32 juta')->assertSee('15 Oktober 2026')->assertSee('11032200304330006')->assertSee('application/ld+json', false)->assertSee('lang="id"', false);
    }

    public function test_filters_combine_month_and_duration_and_sort_by_price(): void
    {
        $this->travelTo(Carbon::parse('2026-10-10'));
        $this->seed(PackageSeeder::class);

        $this->get('/paket-umrah?month=2026-12&days=9&sort=price')->assertOk()
            ->assertSee('3 pilihan keberangkatan')->assertSeeInOrder(['Rp32.000.000', 'Rp34.000.000'])
            ->assertDontSee('06 Desember 2026')->assertSee('noindex,follow')->assertSee('href="'.route('packages.index').'"', false);
    }

    public function test_invalid_filters_return_validation_errors(): void
    {
        $this->from('/paket-umrah')->get('/paket-umrah?month=invalid&days=3&sort=unsafe')->assertRedirect('/paket-umrah')
            ->assertSessionHasErrors(['month' => 'Pilih bulan keberangkatan yang valid.', 'days' => 'Pilih durasi 9 atau 12 hari.', 'sort' => 'Pilihan urutan tidak valid.']);
    }

    public function test_expired_and_unpublished_packages_are_excluded_from_catalogue_and_sitemap(): void
    {
        $this->travelTo(Carbon::parse('2026-10-10'));
        $past = Package::factory()->create(['departure_date' => today()->subDay(), 'name' => 'Paket sudah lewat']);
        $draft = Package::factory()->create(['is_published' => false, 'name' => 'Paket belum terbit']);
        $current = Package::factory()->create(['name' => 'Paket aktif']);

        $this->get('/paket-umrah')->assertOk()->assertSee('Paket aktif')->assertDontSee('Paket sudah lewat')->assertDontSee('Paket belum terbit');
        $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee($current->slug)->assertDontSee($past->slug)->assertDontSee($draft->slug);
        $this->get(route('packages.show', $draft->slug))->assertNotFound();
        $this->get(route('packages.show', $past->slug))->assertOk()->assertSee('Jadwal ini sudah lewat.')->assertDontSee('Tanyakan ketersediaan');
    }

    public function test_detail_shows_price_hotels_and_encoded_whatsapp_consultation(): void
    {
        $package = Package::factory()->create(['name' => 'Umrah <script>alert(1)</script>']);

        $this->get(route('packages.show', $package->slug))->assertOk()->assertSee('Rp32.000.000')->assertSee('Arguan Al Salam')
            ->assertSee('Grand Al Masya')->assertSee($package->whatsappUrl())->assertSee('Umrah <script>alert(1)</script>')
            ->assertDontSee('<script>alert(1)</script>', false);
        $this->get('/paket-umrah/tidak-ada')->assertNotFound();
    }

    public function test_empty_catalogue_has_helpful_next_step(): void
    {
        $this->get('/paket-umrah')->assertOk()->assertSee('Belum ada jadwal yang sesuai.')->assertSee('Tanyakan jadwal');
    }

    public function test_informational_pages_render_and_contact_uses_actual_number(): void
    {
        foreach (['tentang-kami', 'umrah-jogja', 'kontak', 'panduan-umrah', 'privasi'] as $path) {
            $this->get('/'.$path)->assertOk()->assertSee('0821-3604-2015');
        }
        $this->get('/kontak')->assertSee('data-phone="6282136042015"', false);
    }

    public function test_indexing_is_explicitly_enabled_for_production(): void
    {
        config(['safir.indexable' => false]);
        $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /');
        $this->get('/tentang-kami')->assertSee('noindex,follow');

        config(['safir.indexable' => true]);
        $this->get('/robots.txt')->assertOk()->assertSee('Allow: /')->assertSee(route('sitemap'));
        $this->get('/tentang-kami')->assertSee('index,follow,max-image-preview:large');
    }

    public function test_seed_is_repeatable_without_duplicate_departures(): void
    {
        $this->seed(PackageSeeder::class);
        $this->seed(PackageSeeder::class);

        $this->assertDatabaseCount('packages', 11);
        $this->assertDatabaseHas('packages', ['slug' => 'umrah-9-hari-thaif-2026-12-03', 'airline' => 'Saudia', 'price' => 34000000]);
        $this->assertDatabaseHas('packages', ['slug' => 'umrah-9-hari-2026-12-03', 'airline' => 'Lion Air', 'price' => 32000000]);
    }

    public function test_month_filter_includes_the_last_day_of_the_month(): void
    {
        $this->travelTo(Carbon::parse('2026-10-10'));
        Package::factory()->create(['departure_date' => '2026-12-31', 'name' => 'Akhir Desember']);
        Package::factory()->create(['departure_date' => '2027-01-01', 'name' => 'Awal Januari']);

        $this->get('/paket-umrah?month=2026-12')->assertOk()->assertSee('Akhir Desember')->assertDontSee('Awal Januari');
    }
}
