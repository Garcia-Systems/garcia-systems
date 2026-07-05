<?php

namespace Tests\Feature;

use App\Models\Capability;
use App\Models\CompanyType;
use App\Models\Department;
use App\Models\FrictionPoint;
use App\Models\Industry;
use App\Models\SolutionPattern;
use App\Models\User;
use App\Models\Workflow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAtlasTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_users_cannot_access_atlas_admin_routes(): void
    {
        $this->get('/admin/atlas/industries')->assertRedirect('/login');
    }

    public function test_authenticated_users_can_list_atlas_resources(): void
    {
        Industry::create(['name' => 'Healthcare', 'slug' => 'healthcare']);

        $this->actingAs(User::factory()->create())
            ->get('/admin/atlas/industries')
            ->assertOk()
            ->assertSee('Healthcare');
    }

    public function test_authenticated_users_can_create_and_edit_records(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/admin/atlas/industries', [
            'name' => 'Professional Services',
            'slug' => '',
            'description' => 'Service firms.',
        ]);

        $industry = Industry::where('slug', 'professional-services')->firstOrFail();
        $response->assertRedirect(route('admin.atlas.edit', ['industries', $industry]));

        $this->actingAs($user)->put(route('admin.atlas.update', ['industries', $industry]), [
            'name' => 'Updated Services',
            'slug' => 'updated-services',
            'description' => 'Updated description.',
        ])->assertRedirect();

        $this->assertDatabaseHas('industries', ['id' => $industry->id, 'name' => 'Updated Services', 'slug' => 'updated-services']);
    }

    public function test_relationship_saving_works(): void
    {
        $user = User::factory()->create();
        $industry = Industry::create(['name' => 'Retail', 'slug' => 'retail']);
        $companyType = CompanyType::create(['industry_id' => $industry->id, 'name' => 'Multi-location', 'slug' => 'multi-location']);
        $department = Department::create(['company_type_id' => $companyType->id, 'name' => 'Operations', 'slug' => 'operations']);
        $capability = Capability::create(['name' => 'Reporting', 'slug' => 'reporting', 'description' => 'Reporting']);
        $pattern = SolutionPattern::create(['name' => 'Dashboard', 'slug' => 'dashboard', 'description' => 'Dashboard']);

        $this->actingAs($user)->post('/admin/atlas/workflows', [
            'department_id' => $department->id,
            'name' => 'Inventory review',
            'slug' => '',
            'description' => 'Review inventory levels.',
        ])->assertRedirect();

        $workflow = Workflow::where('slug', 'inventory-review')->firstOrFail();
        $this->assertTrue($workflow->department()->is($department));

        $this->actingAs($user)->put(route('admin.atlas.update', ['solution-patterns', $pattern]), [
            'name' => 'Dashboard',
            'slug' => 'dashboard',
            'description' => 'Dashboard',
            'capability_ids' => [$capability->id],
        ])->assertRedirect();
        $this->assertTrue($pattern->fresh()->capabilities()->whereKey($capability->id)->exists());

        $this->actingAs($user)->post('/admin/atlas/friction-points', [
            'workflow_id' => $workflow->id,
            'name' => 'Manual counts',
            'slug' => '',
            'description' => 'Manual inventory counts.',
            'impact' => 'Slow decisions.',
            'solution_pattern_ids' => [$pattern->id],
        ])->assertRedirect();

        $frictionPoint = FrictionPoint::where('slug', 'manual-counts')->firstOrFail();
        $this->assertTrue($frictionPoint->solutionPatterns()->whereKey($pattern->id)->exists());
    }

    public function test_deleting_parent_records_is_handled_safely(): void
    {
        $user = User::factory()->create();
        $industry = Industry::create(['name' => 'Logistics', 'slug' => 'logistics']);
        CompanyType::create(['industry_id' => $industry->id, 'name' => 'Carrier', 'slug' => 'carrier']);

        $this->actingAs($user)->delete(route('admin.atlas.destroy', ['industries', $industry]))
            ->assertRedirect()
            ->assertSessionHasErrors();

        $this->assertDatabaseHas('industries', ['id' => $industry->id]);
    }
}
