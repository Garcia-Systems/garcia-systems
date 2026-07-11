<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class TrustedProxyHttpsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware('web')->get('/__proxy_https_probe', function () {
            session(['proxy_https_probe' => 'active']);

            return response()->json([
                'secure' => request()->secure(),
                'url' => url('/proxy-target'),
            ]);
        });

        Route::middleware('web')->get('/__proxy_https_redirect', fn () => redirect('/proxy-target'));
    }

    public function test_it_detects_secure_forwarded_proxy_requests_and_generates_https_urls(): void
    {
        $response = $this->withHeaders([
            'X-Forwarded-Proto' => 'https',
            'X-Forwarded-Host' => 'garcia-systems.example',
            'X-Forwarded-Port' => '443',
        ])->get('http://internal/__proxy_https_probe');

        $response->assertOk()
            ->assertJson([
                'secure' => true,
                'url' => 'https://garcia-systems.example/proxy-target',
            ]);

        $this->assertTrue($response->headers->getCookies()[0]->isSecure());
    }

    public function test_it_keeps_local_http_requests_unchanged_without_forwarded_https_headers(): void
    {
        $response = $this->get('http://localhost/__proxy_https_probe');

        $response->assertOk()
            ->assertJson([
                'secure' => false,
                'url' => 'http://localhost/proxy-target',
            ]);

        $this->assertFalse($response->headers->getCookies()[0]->isSecure());
    }

    public function test_it_keeps_redirects_on_https_when_the_proxy_reports_https(): void
    {
        $this->withHeaders([
            'X-Forwarded-Proto' => 'https',
            'X-Forwarded-Host' => 'garcia-systems.example',
            'X-Forwarded-Port' => '443',
        ])->get('http://internal/__proxy_https_redirect')
            ->assertRedirect('https://garcia-systems.example/proxy-target');
    }
}
