<?php

namespace Database\Seeders;

use App\Models\{AssessmentQuestion,Capability,Category,CompanyType,Department,Industry,SolutionPattern};
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LookupReferenceSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedCategories();
        $this->seedCapabilitiesAndPatterns();
        $this->seedAtlasLookups();
        $this->seedAssessmentQuestions();
    }

    private function seedCategories(): void
    {
        foreach ([
            ['Strategy', 'Practical AI, automation, and systems planning.'],
            ['Operations', 'Workflow and operating-system improvement.'],
            ['Data & Reporting', 'Better decisions through cleaner data and useful reporting.'],
            ['Product Systems', 'Turning workflow problems into useful internal products.'],
            ['AI Readiness', 'Grounded preparation for intelligent workflows.'],
        ] as [$name, $description]) {
            Category::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'description' => $description]);
        }
    }

    private function seedCapabilitiesAndPatterns(): void
    {
        foreach (['Automation','Reporting','Data Quality','Workflow Visibility','Knowledge Management','Vendor Coordination','Systems Integration','Intake Management','Approval Routing','Records Management','Exception Handling','Operational Dashboards'] as $name) {
            Capability::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'description' => $name.' capability for practical workflow modernization.']);
        }

        foreach ([
            ['Structured intake and routing', ['automation','workflow-visibility','intake-management']],
            ['Operational dashboard', ['reporting','workflow-visibility','operational-dashboards']],
            ['Shared knowledge base', ['knowledge-management','data-quality']],
            ['Vendor coordination hub', ['vendor-coordination','automation','workflow-visibility']],
            ['Data cleanup workflow', ['data-quality','reporting','records-management']],
            ['Approval rules and exception queue', ['approval-routing','exception-handling','automation']],
            ['System-of-record clarification', ['systems-integration','data-quality','records-management']],
            ['Cross-system status layer', ['systems-integration','workflow-visibility','operational-dashboards']],
        ] as [$name, $capabilitySlugs]) {
            $pattern = SolutionPattern::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'description' => $name.' pattern for reducing operational friction while preserving human ownership.']);
            $pattern->capabilities()->sync(Capability::whereIn('slug', $capabilitySlugs)->pluck('id')->all());
        }
    }

    private function seedAtlasLookups(): void
    {
        foreach (['Healthcare','Education','Logistics','Retail','Manufacturing','Government','Professional Services'] as $name) {
            Industry::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'description' => 'Opportunity mapping context for '.$name.' organizations improving products, systems, and intelligent workflows.']);
        }
        foreach (['Small business','Growing mid-market team','Multi-location operator','Public agency','Enterprise division','Professional practice','Regional service provider'] as $name) {
            CompanyType::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'description' => 'Common organization type for Garcia Systems discovery.']);
        }
        foreach (['Operations','Sales','Finance','Customer Support','Procurement','Compliance','Human Resources','IT','Field Operations','Administration','Clinical Operations','Student Services'] as $name) {
            Department::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'description' => 'Business function with recurring systems, workflow, and coordination work.']);
        }
    }

    private function seedAssessmentQuestions(): void
    {
        foreach ([
            ['Workflow documentation', 'Do you have clearly documented workflows?'],
            ['Data readiness', 'Is your operational data organized and accessible?'],
            ['Pilot selection', 'Can your team define measurable success for an AI or automation pilot?'],
            ['Stakeholder alignment', 'Do process owners have time to support implementation?'],
        ] as $index => [$category, $question]) {
            AssessmentQuestion::updateOrCreate(['question' => $question], ['category' => $category, 'help_text' => 'Use your current operating reality, not an ideal future state.', 'sort_order' => $index + 1]);
        }
    }
}
