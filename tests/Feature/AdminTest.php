<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Package;
use App\Models\Page;
use App\Models\Testimonial;
use App\Models\User;
use Database\Seeders\SiteContentSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function articleData(array $overrides = []): array
    {
        return array_replace(['title' => 'Kabar perjalanan umrah', 'slug' => 'kabar-perjalanan-umrah', 'excerpt' => 'Informasi persiapan untuk jamaah.', 'body' => '## Persiapan'."\n\n".'Hubungi tim Safir.', 'author' => 'Redaksi Safir', 'is_published' => 0], $overrides);
    }

    public function test_guests_and_non_admins_cannot_read_or_write_back_office(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->post('/admin/content/articles', $this->articleData())->assertRedirect('/admin/login');
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
        $this->put('/admin/settings?group=seo', ['home_title' => 'Hacked'])->assertForbidden();
        $this->post('/admin/content/articles', $this->articleData())->assertForbidden();
        $this->assertDatabaseCount('articles', 0);
        $this->assertDatabaseCount('site_settings', 0);
    }

    public function test_admin_login_logout_and_non_admin_rejection(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'password' => 'ValidPassword123!']);
        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'ValidPassword123!'])->assertRedirect('/admin');
        $this->assertAuthenticatedAs($admin);
        $this->post('/admin/logout')->assertRedirect('/admin/login');
        $this->assertGuest();
        $normal = User::factory()->create(['password' => 'ValidPassword123!']);
        $this->post('/admin/login', ['email' => $normal->email, 'password' => 'ValidPassword123!'])->assertSessionHasErrors(['email' => 'Email atau kata sandi tidak sesuai.']);
        $this->assertGuest();
    }

    public function test_failed_login_attempts_are_rate_limited(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/admin/login', ['email' => 'unknown@example.test', 'password' => 'bad-password'])->assertSessionHasErrors('email');
        }
        $this->post('/admin/login', ['email' => 'unknown@example.test', 'password' => 'bad-password'])->assertSessionHasErrors(['email' => 'Terlalu banyak percobaan. Coba lagi setelah satu menit.']);
        $this->assertGuest();
    }

    public function test_all_admin_screens_render_and_are_not_indexable(): void
    {
        $this->actingAs($this->admin());
        foreach (['/admin', '/admin/account', '/admin/settings', '/admin/settings?group=seo', '/admin/settings?group=copy1'] as $url) {
            $this->get($url)->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        }
        foreach (['packages', 'articles', 'testimonials', 'pages'] as $section) {
            $this->get('/admin/content/'.$section)->assertOk();
            $this->get('/admin/content/'.$section.'/create')->assertOk();
        }
        $this->get('/admin/content/users')->assertNotFound();
    }

    public function test_article_create_publish_edit_and_delete_updates_public_pages_and_sitemap(): void
    {
        $this->actingAs($this->admin());
        $this->post('/admin/content/articles', $this->articleData())->assertSessionHasNoErrors()->assertRedirect();
        $article = Article::sole();
        $this->get('/berita/'.$article->slug)->assertNotFound();
        $this->put('/admin/content/articles/'.$article->id, $this->articleData(['is_published' => 1, 'published_at' => now()->subMinute()->format('Y-m-d H:i:s'), 'seo_title' => 'Informasi Umrah Safir']))->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('articles', ['id' => $article->id, 'is_published' => true]);
        $this->get('/berita/'.$article->slug)->assertOk()->assertSee('Informasi Umrah Safir')->assertSee('NewsArticle')->assertSee('<h2>Persiapan</h2>', false);
        $this->get('/sitemap.xml')->assertOk()->assertSee($article->slug)->assertSee('lastmod');
        $this->get('/')->assertOk()->assertSee($article->title);
        $this->delete('/admin/content/articles/'.$article->id)->assertRedirect('/admin/content/articles');
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
        $this->get('/berita/'.$article->slug)->assertNotFound();
    }

    public function test_scheduled_article_is_hidden_until_its_publication_time(): void
    {
        $this->freezeTime();
        $article = Article::factory()->published()->create(['published_at' => now()->addHour()]);
        $this->get('/berita/'.$article->slug)->assertNotFound();
        $this->get('/sitemap.xml')->assertDontSee($article->slug);
        $this->travel(2)->hours();
        $this->get('/berita/'.$article->slug)->assertOk();
        $this->get('/sitemap.xml')->assertSee($article->slug);
    }

    public function test_validation_rejects_missing_publication_date_unsafe_sources_and_changed_slugs(): void
    {
        $this->actingAs($this->admin())->post('/admin/content/articles', $this->articleData(['is_published' => 1, 'source_url' => 'javascript:alert(1)']))->assertSessionHasErrors(['published_at', 'source_url']);
        $this->assertDatabaseCount('articles', 0);
        $article = Article::factory()->create(['slug' => 'tetap']);
        $this->put('/admin/content/articles/'.$article->id, $this->articleData(['slug' => 'berubah']))->assertSessionHasErrors('slug');
        $this->assertDatabaseHas('articles', ['id' => $article->id, 'slug' => 'tetap']);
    }

    public function test_testimonials_require_consent_and_publication(): void
    {
        $data = ['name' => 'Jamaah Uji', 'city' => 'Bantul', 'body' => 'Pengalaman perjalanan untuk pengujian.', 'rating' => 5, 'is_published' => 1];
        $this->actingAs($this->admin())->post('/admin/content/testimonials', $data)->assertSessionHasErrors('consent_confirmed');
        $this->assertDatabaseCount('testimonials', 0);
        $this->post('/admin/content/testimonials', [...$data, 'consent_confirmed' => 1])->assertSessionHasNoErrors();
        $this->get('/testimoni')->assertOk()->assertSee('Jamaah Uji');
        $record = Testimonial::sole();
        $this->put('/admin/content/testimonials/'.$record->id, [...$data, 'is_published' => 0, 'consent_confirmed' => 1])->assertSessionHasNoErrors();
        $this->get('/testimoni')->assertDontSee('Jamaah Uji');
    }

    public function test_settings_change_public_text_and_seo_without_allowing_injected_html(): void
    {
        $this->actingAs($this->admin())->put('/admin/settings?group=seo', [...config('seo'), 'home_title' => 'Judul SEO Baru'])->assertSessionHasNoErrors();
        $copy = collect(config('content'))->filter(fn ($v, $k) => str_starts_with($k, 'copy1_'))->all();
        $this->put('/admin/settings?group=copy1', [...$copy, 'copy1_002' => '<script>alert(1)</script>'])->assertSessionHasNoErrors();
        $this->get('/')->assertOk()->assertSee('Judul SEO Baru')->assertSee('<script>alert(1)</script>')->assertDontSee('<script>alert(1)</script>', false);
        $this->assertDatabaseHas('site_settings', ['key' => 'home_title', 'value' => 'Judul SEO Baru']);
    }

    public function test_markdown_strips_raw_html_and_unsafe_links(): void
    {
        $article = Article::factory()->published()->create(['body' => '<script>alert(1)</script>'."\n\n".'[Klik](javascript:alert(1))']);
        $this->get('/berita/'.$article->slug)->assertOk()->assertDontSee('<script>alert(1)</script>', false)->assertDontSee('href="javascript:', false);
    }

    public function test_image_upload_is_validated_and_served_as_non_executable_webp(): void
    {
        Storage::fake('local');
        $this->actingAs($this->admin())->post('/admin/content/articles', $this->articleData(['image_upload' => UploadedFile::fake()->create('bad.svg', 1, 'image/svg+xml')]))->assertSessionHasErrors('image_upload');
        $this->assertDatabaseCount('articles', 0);
        $this->post('/admin/content/articles', $this->articleData(['image_upload' => UploadedFile::fake()->image('cover.png', 400, 300)]))->assertSessionHasNoErrors();
        $article = Article::sole();
        Storage::disk('local')->assertExists('media/'.$article->image);
        $this->get('/media/'.$article->image)->assertOk()->assertHeader('Content-Type', 'image/webp')->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->get('/media/not-a-valid-file.php')->assertNotFound();
    }

    public function test_core_pages_are_editable_and_can_be_hidden_but_not_deleted(): void
    {
        $this->seed(SiteContentSeeder::class);
        $page = Page::where('slug', 'tentang-kami')->firstOrFail();
        $this->actingAs($this->admin())->put('/admin/content/pages/'.$page->id, ['title' => 'Profil Diperbarui', 'slug' => $page->slug, 'body' => 'Isi profil terbaru.', 'is_published' => 1])->assertSessionHasNoErrors();
        $this->get('/tentang-kami')->assertOk()->assertSee('Profil Diperbarui')->assertSee('Isi profil terbaru.');
        $this->delete('/admin/content/pages/'.$page->id)->assertStatus(422);
        $this->assertModelExists($page);
        $this->put('/admin/content/pages/'.$page->id, ['title' => $page->title, 'slug' => $page->slug, 'body' => 'Isi tersembunyi', 'is_published' => 0])->assertSessionHasNoErrors();
        $this->get('/tentang-kami')->assertNotFound();
        $this->get('/sitemap.xml')->assertDontSee('/tentang-kami');
    }

    public function test_package_can_be_managed_from_admin(): void
    {
        $package = Package::factory()->create();
        $data = $package->only(array_keys(config('admin.packages.fields')));
        $data['departure_date'] = $package->departure_date->format('Y-m-d');
        $this->actingAs($this->admin())->put('/admin/content/packages/'.$package->id, [...$data, 'price' => 36000000, 'description' => 'Program baru dari admin.'])->assertSessionHasNoErrors();
        $this->get('/paket-umrah/'.$package->slug)->assertOk()->assertSee('Rp36.000.000')->assertSee('Program baru dari admin.');
        $this->assertDatabaseHas('packages', ['id' => $package->id, 'price' => 36000000]);
    }

    public function test_admin_password_change_requires_current_password(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'password' => 'CurrentPass123!']);
        $this->actingAs($admin)->put('/admin/account/password', ['current_password' => 'wrong', 'password' => 'NextPassword456!', 'password_confirmation' => 'NextPassword456!'])->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('CurrentPass123!', $admin->fresh()->password));
        $this->put('/admin/account/password', ['current_password' => 'CurrentPass123!', 'password' => 'NextPassword456!', 'password_confirmation' => 'NextPassword456!'])->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('NextPassword456!', $admin->fresh()->password));
    }

    public function test_seeded_pages_and_saved_editors_render_correctly(): void
    {
        $this->seed(SiteContentSeeder::class);
        foreach (['tentang-kami', 'umrah-jogja', 'kontak', 'panduan-umrah', 'privasi'] as $slug) {
            $this->get('/'.$slug)->assertOk()->assertDontSee('@if(', false)->assertDontSee('@isset(', false);
        }
        $article = Article::factory()->published()->create();
        $this->actingAs($this->admin())->get('/admin/content/articles/'.$article->id.'/edit')->assertOk()->assertSee($article->title);
    }

    public function test_hidden_page_links_disappear_and_custom_pages_are_discoverable(): void
    {
        Page::factory()->create(['slug' => 'tentang-kami', 'is_published' => false]);
        Page::factory()->create(['slug' => 'layanan-keluarga', 'title' => 'Layanan keluarga']);

        $this->get('/')->assertOk()->assertDontSee('href="'.route('about').'"', false)->assertSee('Layanan keluarga');
        $this->get('/layanan-keluarga')->assertOk()->assertSee('Layanan keluarga');
    }

    public function test_admin_managed_package_duration_is_available_in_front_office_filters(): void
    {
        $package = Package::factory()->create(['days' => 15, 'name' => 'Umrah Lima Belas Hari']);

        $this->get('/paket-umrah?days=15')->assertOk()->assertSee('Umrah Lima Belas Hari')->assertSee('15 hari');
    }

    public function test_business_settings_update_contact_and_reject_unsafe_urls(): void
    {
        $data = [];
        foreach (['name', 'company', 'contact', 'phone', 'license', 'address', 'gunungkidul', 'whatsapp', 'maps'] as $key) {
            $data['business_'.$key] = config('safir.'.$key);
        }
        $this->actingAs($this->admin())->put('/admin/settings', [...$data, 'business_maps' => 'javascript:alert(1)'])->assertSessionHasErrors('business_maps');
        $this->assertDatabaseCount('site_settings', 0);
        $this->put('/admin/settings', [...$data, 'business_contact' => 'Tim Konsultasi', 'business_whatsapp' => '6281234567890', 'business_indexable' => 1])->assertSessionHasNoErrors();
        $this->get('/kontak')->assertOk()->assertSee('Tim Konsultasi')->assertSee('data-phone="6281234567890"', false);
        $this->get('/robots.txt')->assertSee('Allow: /')->assertSee('Disallow: /admin');
    }
}
