<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Package;
use App\Models\Page;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DocumentSeederTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_local_seo_content_is_idempotent_and_links_to_current_packages(): void
    {
        $this->travelTo(now()->setDate(2026, 10, 14));
        $this->seed(DatabaseSeeder::class);
        $body = Page::where('slug', 'umrah-jogja')->firstOrFail()->body;
        $this->seed(DatabaseSeeder::class);
        $this->assertSame($body, Page::where('slug', 'umrah-jogja')->firstOrFail()->body);
        $this->get('/umrah-jogja')->assertOk()->assertSee('Travel Umroh Jogja')
            ->assertSee('15 Oktober 2026')->assertSee('0821-3604-2015')
            ->assertSee('href="/paket-umrah"', false)->assertDontSee('Kantor pelayanan Wonosari');
        config(['services.google_site_verification' => 'verification-test']);
        $this->get('/')->assertOk()->assertSee('name="google-site-verification" content="verification-test"', false);
    }

    public function test_testimonial_metadata_is_valid_and_seed_templates_are_not_public(): void
    {
        $this->seed(DatabaseSeeder::class);
        $response = $this->get('/testimoni')->assertOk()
            ->assertSee('name="twitter:title"', false)
            ->assertSee('name="twitter:description"', false)
            ->assertSee('name="twitter:image"', false)
            ->assertDontSee('[CONTOH]')->assertDontSee('aggregateRating');
        preg_match_all('/<script type="application\/ld\+json">(.*?)<\/script>/s', $response->getContent(), $matches);
        $schemas = array_map(fn ($json) => json_decode($json, true, 512, JSON_THROW_ON_ERROR), $matches[1]);
        $page = collect($schemas)->firstWhere('@type', 'CollectionPage');
        $this->assertSame(route('testimonials'), $page['url']);
        $this->assertSame('id-ID', $page['inLanguage']);
        $this->assertSame('BreadcrumbList', $page['breadcrumb']['@type']);
        $this->assertDatabaseCount('testimonials', 3);
        $this->assertSame(0, Testimonial::where('is_published', true)->count());
    }

    public function test_primary_contact_update_replaces_legacy_details_and_preserves_other_content(): void
    {
        SiteSetting::create(['key' => 'business_contact', 'value' => 'David']);
        SiteSetting::create(['key' => 'business_phone', 'value' => '0813-5003-5003']);
        SiteSetting::create(['key' => 'business_whatsapp', 'value' => '6281350035003']);
        SiteSetting::create(['key' => 'copy4_031', 'value' => 'Konsultasi dengan David. Catatan admin tetap ada.']);
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        foreach (['/', '/kontak', '/privasi', '/paket-umrah/umrah-9-hari-2026-12-03'] as $url) {
            $this->get($url)->assertOk()->assertSee('Wisnu')->assertSee('0821-3604-2015')
                ->assertDontSee('David')->assertDontSee('DAVID')->assertDontSee('6281350035003')->assertDontSee('0813-5003-5003');
        }
        $this->assertDatabaseHas('site_settings', ['key' => 'copy4_031', 'value' => 'Konsultasi dengan Wisnu. Catatan admin tetap ada.']);
        $this->get('/')->assertSee('"telephone":"+6282136042015"', false);
    }

    public function test_seeding_removes_the_old_office_from_existing_content_and_public_pages(): void
    {
        SiteSetting::create(['key' => 'business_gunungkidul', 'value' => 'Jl. Tentara Pelajar No. 9A, Trimulyo I, Kepek, Wonosari, Gunungkidul (Happy Kids Educare)']);
        SiteSetting::create(['key' => 'copy1_053', 'value' => 'Lebih dekat dari Wonosari.']);
        Page::factory()->create(['slug' => 'umrah-jogja', 'is_published' => true, 'body' => "Catatan admin tetap ada.\n\n## Kantor pelayanan Gunungkidul\n{gunungkidul}\n\nKunjungi Bantul."]);
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseMissing('site_settings', ['key' => 'business_gunungkidul']);
        $this->assertStringContainsString('Catatan admin tetap ada.', Page::where('slug', 'umrah-jogja')->firstOrFail()->body);
        foreach (['/', '/kontak', '/umrah-jogja', '/tentang-kami'] as $url) {
            $this->get($url)->assertOk()->assertDontSee('Kantor pelayanan Wonosari')->assertDontSee('Tentara Pelajar')->assertDontSee('Kantor pelayanan Gunungkidul')->assertDontSee('{gunungkidul}');
        }
        config(['services.openai.enabled' => false]);
        $this->postJson('/chat', ['message' => 'Alamat kantor?'])->assertOk()->assertSee('Kantor Bantul')->assertDontSee('Gunungkidul');
    }

    public function test_editorial_drafts_remain_private_and_reseeding_preserves_edits(): void
    {
        $this->seed(DatabaseSeeder::class);
        $article = Article::firstOrFail();
        $testimonial = Testimonial::firstOrFail();
        $this->get('/berita/'.$article->slug)->assertNotFound();
        $this->get('/sitemap.xml')->assertDontSee($article->slug);
        $this->get('/testimoni')->assertDontSee('[CONTOH]');
        $article->update(['title' => 'Draf hasil edit']);
        $testimonial->update(['name' => 'Draf jamaah hasil edit']);

        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('articles', 2);
        $this->assertDatabaseCount('testimonials', 3);
        $this->assertSame('Draf hasil edit', $article->fresh()->title);
        $this->assertSame('Draf jamaah hasil edit', $testimonial->fresh()->name);
        $this->assertFalse($testimonial->fresh()->consent_confirmed);
    }

    public function test_optional_admin_seed_hashes_password_and_does_not_reset_existing_accounts(): void
    {
        config(['seeding.admin_email' => 'seed@example.test', 'seeding.admin_name' => 'Seed Admin', 'seeding.admin_password' => 'StrongPassword!2026']);
        $this->seed(AdminSeeder::class);
        $admin = User::where('email', 'seed@example.test')->firstOrFail();
        $this->assertTrue($admin->is_admin);
        $this->assertTrue(Hash::check('StrongPassword!2026', $admin->password));
        $admin->is_admin = false;
        $admin->save();
        config(['seeding.admin_password' => 'DifferentPassword!2026']);
        $this->seed(AdminSeeder::class);
        $this->assertDatabaseCount('users', 1);
        $this->assertFalse($admin->fresh()->is_admin);
        $this->assertTrue(Hash::check('StrongPassword!2026', $admin->fresh()->password));
    }

    public function test_testimonial_pagination_has_a_self_referencing_canonical(): void
    {
        $this->get('/testimoni?page=2')->assertOk()->assertSee('href="'.route('testimonials', ['page' => 2]).'"', false);
        $this->get('/testimoni?page=invalid')->assertRedirect()->assertSessionHasErrors('page');
    }

    public function test_document_import_populates_public_content_without_invented_records(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('packages', 11);
        $this->assertDatabaseCount('pages', 6);
        $this->assertDatabaseCount('articles', 2);
        $this->assertDatabaseCount('testimonials', 3);
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseHas('site_settings', ['key' => 'business_license', 'value' => '11032200304330006']);
        $this->assertDatabaseMissing('site_settings', ['key' => 'business_indexable']);
        $this->assertDatabaseHas('packages', ['slug' => 'umrah-9-hari-2026-12-03', 'price' => 32000000, 'airline' => 'Lion Air']);
        $this->assertDatabaseHas('packages', ['slug' => 'umrah-9-hari-thaif-2026-12-03', 'price' => 34000000, 'airline' => 'Saudia']);

        $this->get('/paket-umrah/umrah-9-hari-2026-12-03')->assertOk()
            ->assertSee('Paspor dan vaksin.')->assertSee('DP Rp2 juta')->assertSee('Air Zamzam 5 liter.');
        $this->get('/kontak')->assertOk()->assertSee('0821-3604-2015')->assertSee('Wisnu');
    }

    public function test_reimport_preserves_admin_changes_and_does_not_duplicate_records(): void
    {
        $this->seed(DatabaseSeeder::class);
        $settingCount = SiteSetting::count();
        $package = Package::where('slug', 'umrah-9-hari-2026-12-03')->firstOrFail();
        $package->update(['price' => 36000000, 'facilities' => 'Fasilitas diperbarui admin', 'description' => '', 'is_published' => false]);
        Page::where('slug', 'tentang-kami')->update(['body' => 'Profil diperbarui admin']);
        SiteSetting::where('key', 'business_phone')->update(['value' => '081234567890']);

        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('packages', 11);
        $this->assertDatabaseCount('pages', 6);
        $this->assertDatabaseCount('site_settings', $settingCount);
        $this->assertDatabaseHas('packages', ['id' => $package->id, 'price' => 36000000, 'facilities' => 'Fasilitas diperbarui admin', 'description' => '', 'is_published' => false]);
        $this->assertDatabaseHas('pages', ['slug' => 'tentang-kami', 'body' => 'Profil diperbarui admin']);
        $this->assertDatabaseHas('site_settings', ['key' => 'business_phone', 'value' => '081234567890']);
    }

    public function test_import_backfills_legacy_package_details_without_resetting_price(): void
    {
        $package = Package::factory()->create(['slug' => 'umrah-9-hari-2026-12-03', 'price' => 37000000, 'description' => null, 'facilities' => null, 'seo_title' => null, 'seo_description' => null, 'is_published' => false]);

        $this->seed(DatabaseSeeder::class);

        $package->refresh();
        $this->assertSame(37000000, (int) $package->price);
        $this->assertFalse($package->is_published);
        $this->assertStringContainsString('Paspor dan vaksin.', $package->facilities);
        $this->assertNotEmpty($package->description);
        $this->assertStringContainsString('Rp37.000.000', $package->seo_description);
    }
}
