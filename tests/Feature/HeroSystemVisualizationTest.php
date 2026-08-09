<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeroSystemVisualizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_provides_progressive_hero_system_visualization(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('data-hero-system', false)
            ->assertSee('data-hero-system-fallback', false)
            ->assertSee('data-hero-system-canvas', false)
            ->assertSee('Business Problem')
            ->assertSee('People')
            ->assertSee('Workflow')
            ->assertSee('Data')
            ->assertSee('System')
            ->assertSee('Automation')
            ->assertSee('Measurable Outcome')
            ->assertSee('Garcia Systems brings business problems together with people, workflow, and data', false);
    }
}
