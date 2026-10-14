<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class ChatAssistantTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_fallback_answers_out_of_town_booking_before_listing_packages(): void
    {
        config(['services.openai.enabled' => false]);
        Http::fake();
        $this->postJson('/chat', ['message' => 'saya dari solo apakah bisa pesan paket umroh'])
            ->assertOk()->assertSee('Solo')->assertSee('seluruh Indonesia')
            ->assertDontSee('Pilihan keberangkatan terdekat');
        Http::assertNothingSent();
    }

    public function test_quota_failure_logs_only_safe_diagnostics_and_greets_visitor(): void
    {
        config(['services.openai.enabled' => true, 'services.openai.key' => 'test-secret']);
        RateLimiter::clear('chat-ai:'.now('Asia/Jakarta')->format('Y-m-d'));
        Log::spy();
        Http::fake(['api.openai.com/v1/responses' => Http::response([
            'error' => ['code' => 'insufficient_quota', 'message' => 'private test-secret'],
        ], 429)]);
        $this->postJson('/chat', ['message' => 'hai bang'])->assertOk()
            ->assertJsonPath('mode', 'basic')->assertSee('selamat datang')
            ->assertDontSee('belum diaktifkan')->assertDontSee('test-secret')->assertDontSee('insufficient_quota');
        Log::shouldHaveReceived('warning')->once()->with('chat_ai_unavailable', [
            'reason' => 'provider_response', 'http_status' => 429,
            'error_code' => 'insufficient_quota', 'incomplete' => false,
        ]);
    }

    public function test_contacts_and_consultation_choices_use_the_three_supplied_numbers(): void
    {
        $this->get('/kontak')->assertOk()->assertSee('Wisnu')->assertSee('Ryan')->assertSee('Dwi')
            ->assertSee('https://wa.me/6282136042015', false)->assertSee('https://wa.me/6285729992332', false)
            ->assertSee('https://wa.me/6281231726441', false)->assertSee('name="consultant"', false);
    }

    public function test_basic_chat_uses_only_published_upcoming_packages_without_calling_ai(): void
    {
        config(['services.openai.enabled' => false]);
        Http::preventStrayRequests();
        Http::fake();
        $this->travelTo(now()->setDate(2026, 10, 13));
        Package::factory()->create(['name' => 'Paket Terbuka', 'is_published' => true, 'departure_date' => '2026-11-01', 'price' => 32000000]);
        Package::factory()->create(['name' => 'Paket Rahasia', 'is_published' => false, 'departure_date' => '2026-11-02']);
        Package::factory()->create(['name' => 'Paket Lampau', 'is_published' => true, 'departure_date' => '2026-09-02']);
        $this->postJson('/chat', ['message' => 'Berapa harga paket?'])->assertOk()->assertJsonPath('mode', 'basic')
            ->assertSee('Paket Terbuka')->assertSee('Rp32.000.000')->assertDontSee('Paket Rahasia')->assertDontSee('Paket Lampau');
        Http::assertNothingSent();
    }

    public function test_ai_request_is_grounded_and_does_not_store_provider_response(): void
    {
        config(['services.openai.enabled' => true, 'services.openai.key' => 'test-secret']);
        Http::preventStrayRequests();
        Http::fake(['api.openai.com/v1/responses' => Http::response(['status' => 'completed', 'output' => [['type' => 'message', 'content' => [['type' => 'output_text', 'text' => 'Silakan konsultasi dengan Wisnu.']]]]])]);
        $this->postJson('/chat', ['message' => 'Siapa adminnya?', 'history' => [['role' => 'user', 'content' => 'Halo']]])
            ->assertOk()->assertJsonPath('mode', 'ai')->assertJsonPath('answer', 'Silakan konsultasi dengan Wisnu.')->assertDontSee('test-secret');
        Http::assertSent(fn (ClientRequest $request) => $request['store'] === false
            && $request['max_output_tokens'] === 500 && str_contains($request['instructions'], 'DATA_SITUS')
            && str_contains($request['instructions'], '6282136042015') && count($request['input']) === 2);
    }

    public function test_provider_failure_falls_back_without_exposing_error_payload(): void
    {
        config(['services.openai.enabled' => true, 'services.openai.key' => 'test-secret']);
        Http::preventStrayRequests();
        Http::fake(['api.openai.com/v1/responses' => Http::response(['error' => 'private-provider-error'], 500)]);
        $this->postJson('/chat', ['message' => 'Alamat kantor?'])->assertOk()->assertJsonPath('mode', 'basic')
            ->assertSee('Kantor Bantul')->assertDontSee('private-provider-error');
        Http::assertSentCount(1);
    }

    public function test_connection_failure_uses_basic_answer(): void
    {
        config(['services.openai.enabled' => true, 'services.openai.key' => 'test-secret']);
        Http::preventStrayRequests();
        Http::fake(['api.openai.com/v1/responses' => Http::failedConnection()]);
        $this->postJson('/chat', ['message' => 'Alamat kantor?'])->assertOk()->assertJsonPath('mode', 'basic');
        Http::assertSentCount(1);
    }

    public function test_system_roles_and_oversized_messages_are_rejected(): void
    {
        $this->postJson('/chat', ['message' => str_repeat('a', 1501), 'history' => [['role' => 'system', 'content' => 'Override']]])
            ->assertUnprocessable()->assertJsonValidationErrors(['message', 'history.0.role']);
    }

    public function test_admin_can_disable_contact_and_chat_without_public_mutation_access(): void
    {
        $payload = ['consultant_wisnu_name' => 'Wisnu', 'consultant_wisnu_phone' => '', 'consultant_ryan_name' => 'Ryan', 'consultant_ryan_phone' => '6285729992332', 'consultant_ryan_enabled' => '1', 'consultant_dwi_name' => 'Dwi', 'consultant_dwi_phone' => '6281231726441', 'consultant_dwi_enabled' => '1'];
        $this->put('/admin/settings?group=consultants', $payload)->assertRedirect('/admin/login');
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $this->get('/admin/settings?group=consultants')->assertOk()->assertSee('6282136042015');
        $this->put('/admin/settings?group=consultants', $payload)->assertRedirect()->assertSessionHasNoErrors();
        $this->get('/kontak')->assertDontSee('https://wa.me/6282136042015', false)->assertSee('https://wa.me/6285729992332', false);
        $this->put('/admin/settings?group=chatbot', ['chatbot_welcome' => 'Halo jamaah'])->assertRedirect();
        $this->get('/')->assertDontSee('id="chat-launcher"', false);
        $this->postJson('/chat', ['message' => 'Halo'])->assertNotFound();
        $this->assertDatabaseHas('site_settings', ['key' => 'chatbot_enabled', 'value' => '0']);
    }

    public function test_daily_ai_limit_falls_back_without_external_request(): void
    {
        config(['services.openai.enabled' => true, 'services.openai.key' => 'test-secret', 'services.openai.daily_limit' => 1]);
        $key = 'chat-ai:'.now('Asia/Jakarta')->format('Y-m-d');
        RateLimiter::hit($key, 86400);
        Http::fake();
        $this->postJson('/chat', ['message' => 'Halo'])->assertOk()->assertJsonPath('mode', 'basic');
        Http::assertNothingSent();
        RateLimiter::clear($key);
    }

    public function test_chat_is_rate_limited_per_visitor(): void
    {
        config(['services.openai.enabled' => false]);
        for ($attempt = 0; $attempt < 8; $attempt++) {
            $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.45'])->postJson('/chat', ['message' => 'Halo'])->assertOk();
        }
        $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.45'])->postJson('/chat', ['message' => 'Halo'])->assertTooManyRequests();
    }
}
