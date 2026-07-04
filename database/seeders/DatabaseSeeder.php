<?php

namespace Database\Seeders;

use App\Models\{Article,AssessmentQuestion,Capability,Category,Department,FrictionPoint,Industry,SolutionPattern,Tag,Video,Workflow,CompanyType};
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $strategy = Category::create(['name'=>'Strategy','slug'=>'strategy','description'=>'Practical AI and automation planning.']);
        $ops = Category::create(['name'=>'Operations','slug'=>'operations','description'=>'Workflow and systems improvement.']);
        $tags = collect(['AI Readiness','Automation','Workflow Design'])->map(fn($n)=>Tag::create(['name'=>$n,'slug'=>Str::slug($n)]));
        $articles = [
            ['How to Find Automation Opportunities Without Chasing Hype','Start with recurring friction, measurable delays, and decisions that need better context.','A practical automation roadmap starts by observing work. Look for repeated handoffs, spreadsheet reconciliation, status chasing, and decisions delayed by missing information. Then frame each opportunity by business impact, data availability, process stability, and implementation complexity. The best first project is rarely the flashiest one; it is the workflow where a small improvement can be measured quickly and adopted by the team.',$strategy],
            ['The AI Readiness Questions Every Growing Team Should Ask','Before tools, clarify ownership, data quality, workflow stability, and risk tolerance.','AI readiness is less about buying software and more about understanding the operating environment. Teams should ask whether the problem is well defined, whether the process is consistent enough to improve, whether data is available and trustworthy, and whether the organization can support ongoing iteration. These questions help turn interest in AI into an actionable pilot.',$strategy],
            ['A Simple Pattern for Internal Tool MVPs','Ship a narrow workflow, validate adoption, and expand only after the process proves useful.','Internal tools work best when they are built around one painful job. Define the user, the trigger, the decision or output, and the success metric. Build the smallest version that removes a bottleneck, then watch how the team uses it. Strong internal systems grow from repeated feedback, not large speculative scopes.',$ops],
        ];
        foreach($articles as [$title,$excerpt,$body,$cat]){ $a=Article::create(['category_id'=>$cat->id,'title'=>$title,'slug'=>Str::slug($title),'excerpt'=>$excerpt,'body'=>$body,'published_at'=>now()]); $a->tags()->sync($tags->pluck('id')); }
        $industries = collect(['Healthcare','Education','Logistics','Retail','Manufacturing','Government'])
            ->mapWithKeys(fn ($name) => [Str::slug($name) => Industry::create(['name' => $name, 'slug' => Str::slug($name), 'description' => 'Sample '.$name.' opportunity mapping context.'])]);
        $companyTypes = collect(['Small business','Growing mid-market team','Multi-location operator','Public agency','Enterprise division'])
            ->mapWithKeys(fn ($name) => [Str::slug($name) => CompanyType::create(['name' => $name, 'slug' => Str::slug($name), 'description' => 'Common organization type for Garcia Systems discovery.'])]);
        $departments = collect(['Operations','Sales','Finance','Customer Support','Procurement','Compliance'])
            ->mapWithKeys(fn ($name) => [Str::slug($name) => Department::create(['name' => $name, 'slug' => Str::slug($name), 'description' => 'Business function with recurring systems work.'])]);

        $capabilities = collect(['Automation','Reporting','Data Quality','Workflow Visibility','Knowledge Management','Vendor Coordination'])
            ->mapWithKeys(fn ($name) => [Str::slug($name) => Capability::create(['name' => $name, 'slug' => Str::slug($name), 'description' => $name.' capability for practical workflow modernization.'])]);
        $patterns = collect([
            ['Structured intake and routing', ['automation','workflow-visibility']],
            ['Operational dashboard', ['reporting','workflow-visibility']],
            ['Shared knowledge base', ['knowledge-management','data-quality']],
            ['Vendor coordination hub', ['vendor-coordination','automation']],
            ['Data cleanup workflow', ['data-quality','reporting']],
        ])->mapWithKeys(function ($item) use ($capabilities) {
            [$name, $slugs] = $item;
            $pattern = SolutionPattern::create(['name' => $name, 'slug' => Str::slug($name), 'description' => $name.' pattern for reducing operational friction.']);
            $pattern->capabilities()->attach(collect($slugs)->map(fn ($slug) => $capabilities[$slug]->id));
            return [$pattern->slug => $pattern];
        });

        $examples = [
            ['Healthcare','Growing mid-market team','Operations','Patient intake follow-up','Manual Reporting','manual-reporting','Operational dashboard'],
            ['Education','Public agency','Compliance','Grant documentation','Knowledge Silos','knowledge-silos','Shared knowledge base'],
            ['Logistics','Multi-location operator','Operations','Inventory coordination','Inventory Visibility','inventory-visibility','Operational dashboard'],
            ['Retail','Small business','Procurement','Supplier replenishment','Vendor Coordination','vendor-coordination','Vendor coordination hub'],
            ['Manufacturing','Enterprise division','Finance','Production reporting','Duplicate Work','duplicate-work','Structured intake and routing'],
            ['Government','Public agency','Customer Support','Permit request review','Legacy Systems','legacy-systems','Structured intake and routing'],
            ['Healthcare','Multi-location operator','Compliance','Records reconciliation','Data Quality','data-quality','Data cleanup workflow'],
            ['Education','Growing mid-market team','Operations','Student services handoffs','Disconnected Processes','disconnected-processes','Structured intake and routing'],
        ];

        foreach ($examples as [$industryName, $companyTypeName, $departmentName, $workflowName, $frictionName, $frictionSlug, $patternName]) {
            $workflow = Workflow::create([
                'industry_id' => $industries[Str::slug($industryName)]->id,
                'company_type_id' => $companyTypes[Str::slug($companyTypeName)]->id,
                'department_id' => $departments[Str::slug($departmentName)]->id,
                'name' => $workflowName,
                'slug' => Str::slug($workflowName),
                'description' => 'A sample '.$workflowName.' workflow for exploring where systems work can remove bottlenecks.',
            ]);
            $friction = FrictionPoint::create([
                'workflow_id' => $workflow->id,
                'name' => $frictionName,
                'slug' => $frictionSlug,
                'description' => $frictionName.' creates delays, rework, and limited visibility for the team.',
                'impact' => 'Slower decisions, duplicated effort, and harder coordination.',
            ]);
            $friction->solutionPatterns()->attach($patterns[Str::slug($patternName)]->id);
        }

        foreach(['Do you have clearly documented workflows?','Is your operational data organized and accessible?','Can your team define measurable success for an AI or automation pilot?','Do process owners have time to support implementation?'] as $i=>$q) AssessmentQuestion::create(['question'=>$q,'help_text'=>'Use your current operating reality, not an ideal future state.','sort_order'=>$i+1]);
        foreach(['Mapping Workflow Friction','AI Readiness in Plain English'] as $title) Video::create(['title'=>$title,'slug'=>Str::slug($title),'url'=>'https://example.com/videos/'.Str::slug($title),'description'=>'Placeholder video entry for a short Garcia Systems explainer.']);
    }
}
