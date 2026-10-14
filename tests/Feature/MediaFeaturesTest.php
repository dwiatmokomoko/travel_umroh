<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\HeroSlide;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaFeaturesTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_uploads_orders_and_hides_hero_slides(): void
    {
        Storage::fake('local');
        $payload = ['title' => 'Slide Madinah', 'caption' => 'Perjalanan ibadah', 'image_alt' => 'Pemandangan Madinah', 'sort_order' => 2, 'is_published' => 1];
        $this->post('/admin/content/slides', $payload)->assertRedirect('/admin/login');
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $this->post('/admin/content/slides', $payload)->assertSessionHasErrors('image_upload');
        $this->post('/admin/content/slides', [...$payload, 'image_upload' => UploadedFile::fake()->image('madinah.jpg')])->assertSessionHasNoErrors()->assertRedirect();
        $slide = HeroSlide::firstOrFail();
        Storage::disk('local')->assertExists('media/'.$slide->image);
        HeroSlide::create([...$payload, 'title' => 'Slide Pertama', 'sort_order' => 1, 'image' => $slide->image]);
        HeroSlide::create([...$payload, 'title' => 'Slide Rahasia', 'is_published' => false, 'image' => $slide->image]);
        $this->get('/')->assertOk()->assertSeeInOrder(['Slide Pertama', 'Slide Madinah'])->assertDontSee('Slide Rahasia')->assertSee('data-carousel-controls', false);
        $this->get('/admin/content/slides')->assertOk();
        $this->get('/admin/content/slides/'.$slide->id.'/edit')->assertOk();
    }

    public function test_youtube_video_is_rendered_safely_and_invalid_domains_are_rejected(): void
    {
        $article = Article::factory()->published()->create(['youtube_url' => 'https://youtu.be/abcdefghijk']);
        $this->get('/berita/'.$article->slug)->assertOk()->assertSee('https://www.youtube-nocookie.com/embed/abcdefghijk', false);
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $payload = ['title' => 'Video perjalanan', 'slug' => 'video-perjalanan', 'excerpt' => 'Ringkasan video', 'body' => 'Cerita perjalanan', 'author' => 'Redaksi', 'is_published' => false];
        $this->post('/admin/content/articles', [...$payload, 'youtube_url' => 'https://youtube.com.evil.test/watch?v=abcdefghijk'])->assertSessionHasErrors('youtube_url');
        $this->post('/admin/content/articles', [...$payload, 'youtube_url' => 'https://www.youtube.com/shorts/abcdefghijk'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('articles', ['slug' => 'video-perjalanan', 'youtube_url' => 'https://www.youtube.com/shorts/abcdefghijk']);
    }

    public function test_maps_and_airline_assets_render_with_default_hero(): void
    {
        $this->get('/')->assertOk()->assertSee('images/logo-garuda.png', false)->assertSee('images/logo-lion.png', false)->assertSee('images/madinah.jpg', false);
        $this->get('/kontak')->assertOk()->assertSee('https://www.google.com/maps/embed?pb=', false)->assertSee('Peta kantor Safir');
    }
}
