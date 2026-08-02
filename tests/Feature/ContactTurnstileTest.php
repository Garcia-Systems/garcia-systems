<?php

namespace Tests\Feature;

use App\Models\ContactSubmission;
use App\Notifications\ContactSubmissionReceived;
use App\Notifications\LeadSubmitted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ContactTurnstileTest extends TestCase
{
    use RefreshDatabase;

    private const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.turnstile.site_key' => 'test-site-key',
            'services.turnstile.secret_key' => 'test-secret-key',
        ]);
        Notification::fake();
    }

    public function test_successful_verification_stores_inquiry_sends_notifications_and_preserves_redirect(): void
    {
        Http::fake([
            self::VERIFY_URL => Http::response(['success' => true]),
        ]);

        $this->from('/contact')->post('/contact', $this->validPayload())
            ->assertRedirect('/contact')
            ->assertSessionHas('status', 'Thanks — your message has been saved.');

        $this->assertDatabaseHas(ContactSubmission::class, [
            'email' => 'turnstile@example.com',
            'message' => 'Please help us improve this workflow.',
        ]);
        Notification::assertSentOnDemandTimes(LeadSubmitted::class, 1);
        Notification::assertSentOnDemandTimes(ContactSubmissionReceived::class, 1);
        Http::assertSent(fn ($request) => $request->url() === self::VERIFY_URL
            && $request['secret'] === 'test-secret-key'
            && $request['response'] === 'verified-token'
            && $request['remoteip'] === '127.0.0.1');
    }

    public function test_failed_verification_does_not_store_or_notify(): void
    {
        Http::fake([
            self::VERIFY_URL => Http::response(['success' => false]),
        ]);

        $this->from('/contact')->post('/contact', $this->validPayload())
            ->assertRedirect('/contact')
            ->assertSessionHasErrors([
                'contact' => 'We could not verify your submission. Please try again.',
            ])
            ->assertSessionHasInput('email', 'turnstile@example.com');

        $this->assertDatabaseCount(ContactSubmission::class, 0);
        Notification::assertNothingSent();
    }

    public function test_network_failure_does_not_store_or_notify(): void
    {
        Http::fake(function () {
            throw new ConnectionException('Turnstile timed out');
        });

        $this->from('/contact')->post('/contact', $this->validPayload())
            ->assertRedirect('/contact')
            ->assertSessionHasErrors([
                'contact' => 'We could not verify your submission. Please try again.',
            ]);

        $this->assertDatabaseCount(ContactSubmission::class, 0);
        Notification::assertNothingSent();
    }

    public function test_contact_page_renders_widget_or_friendly_configuration_error(): void
    {
        $this->get('/contact')
            ->assertOk()
            ->assertSee('class="cf-turnstile"', false)
            ->assertSee('data-sitekey="test-site-key"', false)
            ->assertSee('https://challenges.cloudflare.com/turnstile/v0/api.js');

        config(['services.turnstile.site_key' => null]);

        $this->get('/contact')
            ->assertOk()
            ->assertSee('The contact form verification service is not configured. Please try again later.')
            ->assertDontSee('class="cf-turnstile"', false);
    }

    private function validPayload(): array
    {
        return [
            'name' => 'Turnstile Lead',
            'email' => 'turnstile@example.com',
            'message' => 'Please help us improve this workflow.',
            'cf-turnstile-response' => 'verified-token',
        ];
    }
}
