<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Admin login')
            ->assertSee('Email address')
            ->assertSee('Password');
    }

    public function test_invalid_credentials_fail(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        $this->from('/login')->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ])->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_invalid_credentials_preserve_email_and_do_not_expose_password(): void
    {
        User::factory()->create([
            'email' => 'preserve@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        $this->from('/login')->post('/login', [
            'email' => 'preserve@example.com',
            'password' => 'wrong-password',
        ])->assertRedirect('/login')
            ->assertSessionHasInput('email', 'preserve@example.com')
            ->assertSessionMissing('_old_input.password');
    }

    public function test_repeated_failed_attempts_trigger_login_throttling(): void
    {
        User::factory()->create([
            'email' => 'throttle@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->from('/login')->post('/login', [
                'email' => 'throttle@example.com',
                'password' => 'wrong-password',
            ])->assertRedirect('/login')
                ->assertSessionHasErrors([
                    'email' => __('auth.failed'),
                ]);
        }

        $response = $this->from('/login')->post('/login', [
            'email' => 'throttle@example.com',
            'password' => 'wrong-password',
        ])->assertRedirect('/login')
            ->assertSessionHasInput('email', 'throttle@example.com')
            ->assertSessionMissing('_old_input.password');

        $this->assertThrottledEmailError($response);

        $this->assertGuest();
    }

    public function test_successful_login_clears_previous_failed_attempts(): void
    {
        $user = User::factory()->create([
            'email' => 'clears@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        for ($attempt = 1; $attempt <= 4; $attempt++) {
            $this->post('/login', [
                'email' => 'clears@example.com',
                'password' => 'wrong-password',
            ])->assertRedirect('/');
        }

        $this->post('/login', [
            'email' => 'clears@example.com',
            'password' => 'correct-password',
        ])->assertRedirect('/admin');

        $this->assertAuthenticatedAs($user);

        $this->post('/logout')->assertRedirect('/login');

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->from('/login')->post('/login', [
                'email' => 'clears@example.com',
                'password' => 'wrong-password',
            ])->assertRedirect('/login')
                ->assertSessionHasErrors([
                    'email' => __('auth.failed'),
                ]);
        }
    }

    public function test_login_throttling_is_isolated_by_email_and_ip_address(): void
    {
        User::factory()->create([
            'email' => 'isolated@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
                ->post('/login', [
                    'email' => 'isolated@example.com',
                    'password' => 'wrong-password',
                ])->assertRedirect('/');
        }

        $response = $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
            ->from('/login')->post('/login', [
                'email' => 'isolated@example.com',
                'password' => 'wrong-password',
            ])->assertRedirect('/login');

        $this->assertThrottledEmailError($response);

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.11'])
            ->from('/login')->post('/login', [
                'email' => 'isolated@example.com',
                'password' => 'wrong-password',
            ])->assertRedirect('/login')
            ->assertSessionHasErrors([
                'email' => __('auth.failed'),
            ]);

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
            ->from('/login')->post('/login', [
                'email' => 'other@example.com',
                'password' => 'wrong-password',
            ])->assertRedirect('/login')
            ->assertSessionHasErrors([
                'email' => __('auth.failed'),
            ]);
    }

    public function test_login_errors_do_not_enumerate_users(): void
    {
        User::factory()->create([
            'email' => 'known@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        $knownUserResponse = $this->from('/login')->post('/login', [
            'email' => 'known@example.com',
            'password' => 'wrong-password',
        ]);

        $unknownUserResponse = $this->from('/login')->post('/login', [
            'email' => 'unknown@example.com',
            'password' => 'wrong-password',
        ]);

        $knownUserResponse->assertSessionHasErrors([
            'email' => __('auth.failed'),
        ]);
        $unknownUserResponse->assertSessionHasErrors([
            'email' => __('auth.failed'),
        ]);
    }

    private function assertThrottledEmailError(TestResponse $response): void
    {
        $response->assertSessionHasErrors('email');

        $messages = session('errors')->get('email');

        $this->assertNotEmpty($messages);
        $this->assertMatchesRegularExpression(
            '/^Too many login attempts\. Please try again in \d+ seconds\.$/',
            $messages[0],
        );
    }

    public function test_valid_admin_can_log_in(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'correct-password',
        ])->assertRedirect('/admin');

        $this->assertAuthenticatedAs($user);
    }

    public function test_unauthenticated_user_cannot_access_admin(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_admin(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin')
            ->assertOk()
            ->assertSee('Garcia Systems CMS');
    }

    public function test_authenticated_user_is_redirected_away_from_login(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/login')
            ->assertRedirect('/admin');
    }

    public function test_logout_works(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/login')
            ->assertSessionHas('status', 'You have been logged out.');

        $this->assertGuest();
    }
}
