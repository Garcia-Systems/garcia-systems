<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicNavigationExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_navigation_renders_expected_links_and_active_state(): void
    {
        $this->get('/services')
            ->assertOk()
            ->assertSee('aria-label="Primary navigation"', false)
            ->assertSee('Home')
            ->assertSee('About')
            ->assertSee('Services')
            ->assertSee('Atlas')
            ->assertSee('Assessment')
            ->assertSee('Articles')
            ->assertSee('Videos')
            ->assertSee('Contact')
            ->assertSee('href="'.route('services').'"', false)
            ->assertSee('aria-current="page"', false)
            ->assertSee('bg-cyan-400 text-slate-950', false)
            ->assertDontSee('href="'.route('admin.index').'"', false);
    }

    public function test_authenticated_navigation_includes_admin_link(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin')
            ->assertOk()
            ->assertSee('href="'.route('admin.index').'"', false)
            ->assertSee('Admin')
            ->assertSee('aria-current="page"', false);
    }

    public function test_mobile_menu_markup_is_available_without_javascript_frameworks(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('<details class="group relative lg:hidden">', false)
            ->assertSee('<summary', false)
            ->assertSee('aria-label="Open primary navigation menu"', false)
            ->assertSee('aria-label="Mobile primary navigation"', false)
            ->assertSee('Menu');
    }

    public function test_expanded_footer_renders_expected_links_and_content(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Garcia Systems')
            ->assertSee('Practical systems, automation, product, and AI readiness consulting')
            ->assertSee('Footer navigation')
            ->assertSee('Footer services navigation')
            ->assertSee('Consulting services')
            ->assertSee('Opportunity Atlas')
            ->assertSee('AI Readiness Assessment')
            ->assertSee('Articles')
            ->assertSee('Contact')
            ->assertSee('Newsletter')
            ->assertSee('https://www.linkedin.com/company/garcia-systems-lcc', false)
            ->assertSee('Garcia Systems on LinkedIn')
            ->assertSee('https://www.youtube.com/@GarciaSystems', false)
            ->assertSee('Garcia Systems on YouTube')
            ->assertSee('https://substack.com/@garciasystems', false)
            ->assertSee('Garcia Systems on Substack')
            ->assertSee('https://github.com/Garcia-Systems', false)
            ->assertSee('Garcia Systems on GitHub')
            ->assertSee('target="_blank"', false)
            ->assertSee('rel="noopener noreferrer"', false)
            ->assertDontSee('href="#"', false)
            ->assertSee('© '.date('Y').' Garcia Systems');
    }

    public function test_homepage_still_renders_successfully(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Turning Business Problems Into Products, Systems, and Intelligent Workflows')
            ->assertSee('Start a conversation')
            ->assertSee('Explore services');
    }
}
