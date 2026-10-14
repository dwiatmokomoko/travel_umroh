<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\SiteSetting;
use Database\Seeders\PageSeeder;
use Database\Seeders\UmrahTopicsSeoSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class UmrahTopicsSeoTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_topics_and_metadata_are_rendered_on_relevant_pages(): void
    {
        $this->seed(PageSeeder::class);
        SiteSetting::create(['key' => 'home_title', 'value' => 'Travel Umroh Jogja & Paket Umrah | Safir Yogyakarta']);
        $this->seed(UmrahTopicsSeoSeeder::class);

        $this->get('/')->assertOk()->assertSee(config('seo.home_title'));
        $this->get('/paket-umrah')->assertOk()->assertSee(config('seo.packages_title'));
        $this->get('/umrah-jogja')->assertOk()
            ->assertSee('Travel umroh Jogja dan agen umroh Jogja')
            ->assertSee('href="/panduan-umrah"', false)
            ->assertSee('href="/paket-umrah?sort=price"', false);
        $this->get('/panduan-umrah')->assertOk()
            ->assertSee('Bagaimana mempersiapkan manasik dan perlengkapan umrah?');
    }

    public function test_reseeding_does_not_duplicate_content_or_replace_admin_metadata(): void
    {
        $this->seed(PageSeeder::class);
        SiteSetting::create(['key' => 'home_title', 'value' => 'Judul pilihan admin']);
        $page = Page::where('slug', 'umrah-jogja')->firstOrFail();
        $page->update(['seo_title' => 'SEO pilihan admin', 'is_published' => false]);
        $this->seed(UmrahTopicsSeoSeeder::class);
        $body = $page->fresh()->body;
        $this->seed(UmrahTopicsSeoSeeder::class);

        $this->assertSame($body, $page->fresh()->body);
        $this->assertSame('SEO pilihan admin', $page->fresh()->seo_title);
        $this->assertFalse($page->fresh()->is_published);
        $this->assertSame('Judul pilihan admin', SiteSetting::where('key', 'home_title')->value('value'));
        $page->update(['body' => 'Konten diperbarui admin']);
        $this->seed(UmrahTopicsSeoSeeder::class);
        $this->assertSame('Konten diperbarui admin', $page->fresh()->body);
    }
}
