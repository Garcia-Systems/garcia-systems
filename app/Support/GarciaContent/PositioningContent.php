<?php

namespace App\Support\GarciaContent;

use App\Models\{AssessmentQuestion, Capability, Category, CompanyType, Department, Industry, SolutionPattern};
use Illuminate\Support\Str;

final class PositioningContent
{
    /** @return array<int, array{model: class-string, key: string, identity: array<string, mixed>, values: array<string, mixed>, capabilities?: array<int, string>}> */
    public static function records(): array
    {
        $records = [];

        foreach ([
            ['Strategy', 'Business-first options analysis across workflows, systems, economics, and implementation.'],
            ['Operations', 'Workflow and operating-system improvement.'],
            ['Data & Reporting', 'Better decisions through cleaner data and useful reporting.'],
            ['Product Systems', 'Turning workflow problems into useful internal products.'],
            ['AI Readiness', 'A specialized lens for evaluating useful, responsible AI within broader solution options.'],
        ] as [$name, $description]) {
            $records[] = self::slugRecord(Category::class, 'category', $name, $description);
        }

        foreach (['Automation', 'Reporting', 'Data Quality', 'Workflow Visibility', 'Knowledge Management', 'Vendor Coordination', 'Systems Integration', 'Intake Management', 'Approval Routing', 'Records Management', 'Exception Handling', 'Operational Dashboards'] as $name) {
            $records[] = self::slugRecord(Capability::class, 'capability', $name, $name.' capability considered within a practical, business-first solution path.');
        }

        foreach ([
            ['Structured intake and routing', ['automation', 'workflow-visibility', 'intake-management']],
            ['Operational dashboard', ['reporting', 'workflow-visibility', 'operational-dashboards']],
            ['Shared knowledge base', ['knowledge-management', 'data-quality']],
            ['Vendor coordination hub', ['vendor-coordination', 'automation', 'workflow-visibility']],
            ['Data cleanup workflow', ['data-quality', 'reporting', 'records-management']],
            ['Approval rules and exception queue', ['approval-routing', 'exception-handling', 'automation']],
            ['System-of-record clarification', ['systems-integration', 'data-quality', 'records-management']],
            ['Cross-system status layer', ['systems-integration', 'workflow-visibility', 'operational-dashboards']],
        ] as [$name, $capabilities]) {
            $record = self::slugRecord(SolutionPattern::class, 'solution-pattern', $name, $name.' pattern for addressing operating friction while preserving adoption, measurement, and human ownership.');
            $record['capabilities'] = $capabilities;
            $records[] = $record;
        }

        foreach (['Public Health', 'E-commerce', 'Restaurants', 'Hospitality & Tourism', 'Construction & Trades', 'Retail', 'Professional Services', 'Healthcare', 'Education', 'Logistics', 'Manufacturing', 'Government'] as $name) {
            $records[] = self::slugRecord(Industry::class, 'industry', $name, 'Applied Systems Lab context for examining workflows, friction, business questions, and implementation choices in '.$name.'.');
        }
        foreach (['Small business', 'Growing mid-market team', 'Multi-location operator', 'Public agency', 'Enterprise division', 'Professional practice', 'Regional service provider'] as $name) {
            $records[] = self::slugRecord(CompanyType::class, 'company-type', $name, 'Common organization type for Garcia Systems discovery.');
        }
        foreach (['Operations', 'Sales', 'Finance', 'Customer Support', 'Procurement', 'Compliance', 'Human Resources', 'IT', 'Field Operations', 'Administration', 'Clinical Operations', 'Student Services'] as $name) {
            $records[] = self::slugRecord(Department::class, 'department', $name, 'Business function with recurring systems, workflow, and coordination work.');
        }

        foreach ([
            ['workflow_documentation', 'Workflow documentation', 'Is the workflow and business problem clearly documented?', 'Do you have clearly documented workflows?'],
            ['data_readiness', 'Data readiness', 'Is your operational data organized and accessible?', 'Is your operational data organized and accessible?'],
            ['pilot_selection', 'Pilot selection', 'Can your team define measurable value and compare AI with available alternatives?', 'Can your team define measurable success for an AI or automation pilot?'],
            ['stakeholder_alignment', 'Stakeholder alignment', 'Do process owners have time to support implementation?', 'Do process owners have time to support implementation?'],
        ] as $index => [$key, $category, $question, $legacyQuestion]) {
            $records[] = [
                'model' => AssessmentQuestion::class,
                'key' => 'assessment-question.'.$key,
                'identity' => ['key' => $key],
                'legacy_identity' => ['question' => $legacyQuestion],
                'values' => ['category' => $category, 'question' => $question, 'help_text' => 'Use your current operating reality, not an ideal future state.', 'sort_order' => $index + 1, 'is_active' => true],
            ];
        }

        return $records;
    }

    private static function slugRecord(string $model, string $prefix, string $name, string $description): array
    {
        $slug = Str::slug($name);

        return ['model' => $model, 'key' => $prefix.'.'.$slug, 'identity' => ['slug' => $slug], 'values' => ['name' => $name, 'description' => $description]];
    }
}
