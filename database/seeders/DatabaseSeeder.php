<?php

namespace Database\Seeders;

use App\Models\{Article,AssessmentQuestion,Capability,Category,Department,FrictionPoint,Industry,SolutionPattern,Tag,User,Video,Workflow,CompanyType};
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminEmail = env('ADMIN_EMAIL');
        $adminPassword = env('ADMIN_PASSWORD');

        if ($adminEmail && $adminPassword) {
            User::updateOrCreate(
                ['email' => $adminEmail],
                [
                    'name' => env('ADMIN_NAME', 'Garcia Systems Admin'),
                    'password' => Hash::make($adminPassword),
                    'email_verified_at' => now(),
                ]
            );
        }

        $categories = collect([
            ['Strategy', 'Practical AI, automation, and systems planning.'],
            ['Operations', 'Workflow and operating-system improvement.'],
            ['Data & Reporting', 'Better decisions through cleaner data and useful reporting.'],
            ['Product Systems', 'Turning workflow problems into useful internal products.'],
            ['AI Readiness', 'Grounded preparation for intelligent workflows.'],
        ])->mapWithKeys(fn ($category) => [Str::slug($category[0]) => Category::create([
            'name' => $category[0],
            'slug' => Str::slug($category[0]),
            'description' => $category[1],
        ])]);

        $tags = collect([
            'AI Readiness',
            'Automation',
            'Workflow Design',
            'Data Quality',
            'Reporting',
            'Systems Strategy',
            'Internal Tools',
            'Operations',
            'Knowledge Management',
            'Vendor Coordination',
        ])->mapWithKeys(fn ($name) => [Str::slug($name) => Tag::create(['name' => $name, 'slug' => Str::slug($name)])]);

        $articles = [
            ['How to Find Automation Opportunities Without Chasing Hype', 'Start with recurring friction, measurable delays, and decisions that need better context before choosing an automation tool.', 'Practical automation starts by observing work as it happens. Look for repeated handoffs, spreadsheet reconciliation, status chasing, and decisions delayed by missing information. Then frame each opportunity by business impact, data availability, process stability, and implementation complexity. The best first project is rarely the flashiest one; it is the workflow where a small improvement can be measured quickly and adopted by the team.', 'strategy', ['automation', 'workflow-design', 'systems-strategy'], '2026-01-08'],
            ['The AI Readiness Questions Every Growing Team Should Ask', 'Before evaluating AI tools, clarify ownership, data quality, workflow stability, risk tolerance, and success metrics.', 'AI readiness is less about buying software and more about understanding the operating environment. Teams should ask whether the problem is well defined, whether the process is consistent enough to improve, whether data is available and trustworthy, and whether the organization can support ongoing iteration. These questions help turn interest in AI into an actionable pilot.', 'ai-readiness', ['ai-readiness', 'data-quality', 'systems-strategy'], '2026-01-15'],
            ['A Simple Pattern for Internal Tool MVPs', 'Ship a narrow workflow, validate adoption, and expand only after the process proves useful to the team.', 'Internal tools work best when they are built around one painful job. Define the user, the trigger, the decision or output, and the success metric. Build the smallest version that removes a bottleneck, then watch how the team uses it. Strong internal systems grow from repeated feedback, not large speculative scopes.', 'product-systems', ['internal-tools', 'workflow-design', 'operations'], '2026-01-22'],
            ['Manual Reporting Is Usually a Workflow Problem', 'Recurring report work often signals disconnected systems, unclear ownership, and data definitions that need attention.', 'When reporting takes too long, the problem is rarely the dashboard alone. Teams may be copying data between tools, reconciling inconsistent definitions, and chasing approvals before numbers can be trusted. A useful reporting project maps the workflow behind the report, clarifies ownership, and reduces manual preparation before adding more charts.', 'data-reporting', ['reporting', 'data-quality', 'workflow-design'], '2026-02-05'],
            ['Inventory Visibility Without a Full Platform Rebuild', 'Teams can improve stock, supply, and location visibility by starting with the decisions that current data fails to support.', 'Inventory visibility projects should begin with practical questions: what needs to be available, where is it located, who updates the status, and what decision changes when the information is accurate. The first useful system may be a focused coordination layer that cleans up inputs and exposes exceptions before a larger platform investment is justified.', 'operations', ['operations', 'data-quality', 'internal-tools'], '2026-02-12'],
            ['Records Reconciliation as a Product Opportunity', 'Reconciliation work can become a structured product workflow instead of a recurring scramble across spreadsheets and inboxes.', 'Records reconciliation is often treated as back-office cleanup, but it can reveal a product opportunity. Teams need clear intake rules, matching logic, exception queues, and audit-friendly notes. Designing reconciliation as a workflow product reduces rework and creates a stronger foundation for reporting, compliance, and automation.', 'product-systems', ['data-quality', 'internal-tools', 'workflow-design'], '2026-02-19'],
            ['Vendor Coordination Needs More Than Email Threads', 'Procurement and operations teams benefit from shared status, clear requests, and lightweight accountability around vendors.', 'Vendor coordination becomes fragile when requests, documents, and status updates live in separate inboxes. A better pattern is to define request types, required information, owners, due dates, and exception paths. The solution does not need to be complex to create value; it needs to make coordination visible and repeatable.', 'operations', ['vendor-coordination', 'workflow-design', 'operations'], '2026-03-04'],
            ['Data Quality Starts With the Work That Creates Data', 'Cleaner data comes from improving capture, validation, ownership, and feedback loops inside everyday workflows.', 'Data quality programs fail when they focus only on cleanup after the fact. The durable work is closer to the source: improving forms, defaults, validation rules, ownership, and feedback when data is incomplete. Better data quality is an operating habit supported by systems, not a one-time cleanup event.', 'data-reporting', ['data-quality', 'operations', 'workflow-design'], '2026-03-11'],
            ['Duplicate Work Is a Signal, Not Just a Nuisance', 'When teams enter the same information repeatedly, the organization may need clearer systems boundaries and better handoffs.', 'Duplicate work usually means systems do not share enough context or teams do not trust the source of record. Before building an integration, identify why the duplicate entry exists, which system should own the data, and where exceptions should be handled. The right fix may be a workflow change, a focused integration, or a clearer operating rule.', 'operations', ['automation', 'data-quality', 'systems-strategy'], '2026-03-18'],
            ['Disconnected Systems Create Hidden Decision Delays', 'The cost of disconnected tools shows up as slow approvals, missing context, and meetings that exist only to rebuild the picture.', 'Disconnected systems often look manageable until teams need to make cross-functional decisions. People spend time assembling context instead of resolving the issue. A practical modernization roadmap identifies the decisions that suffer, the data they require, and the minimum connection needed to make the work easier.', 'strategy', ['systems-strategy', 'workflow-design', 'operations'], '2026-04-02'],
            ['Approval Delays Are Design Problems', 'Slow approvals can often be reduced by clarifying thresholds, routing rules, decision rights, and exception handling.', 'Approval delays are not always caused by people moving slowly. Often, requests lack required context, decision rights are unclear, or every exception follows the same heavy path. Better approval workflows define thresholds, route requests based on risk, and make missing information visible before work stalls.', 'operations', ['workflow-design', 'automation', 'operations'], '2026-04-09'],
            ['Customer Intake Bottlenecks Before They Become Backlogs', 'A structured intake process helps teams qualify demand, route work, and respond consistently before requests pile up.', 'Customer intake bottlenecks appear when requests arrive through many channels without consistent information or ownership. A useful intake system captures the right context, routes work by type and urgency, and gives teams shared visibility into status. This creates a better experience without pretending every request should be automated.', 'product-systems', ['internal-tools', 'workflow-design', 'operations'], '2026-04-16'],
            ['Knowledge Silos Make Good Teams Slower', 'Shared knowledge systems work when they are tied to real workflows, ownership, and habits for keeping information current.', 'Knowledge management is not solved by creating a folder or wiki. Teams need to know which decisions depend on shared knowledge, who maintains it, and how outdated information is corrected. The best systems put knowledge close to the workflow so people can use it when work is happening.', 'operations', ['knowledge-management', 'workflow-design', 'operations'], '2026-05-07'],
            ['Legacy System Dependency Requires a Transition Plan', 'Legacy systems can stay in place while teams build safer interfaces, reporting layers, and stepwise replacement paths.', 'A legacy system does not always need to be replaced immediately. The first step is understanding which workflows depend on it, where data leaves or enters, and what risks appear during change. A practical plan may add reporting layers, controlled exports, or workflow wrappers while the organization prepares for deeper modernization.', 'strategy', ['systems-strategy', 'data-quality', 'internal-tools'], '2026-05-14'],
            ['Turning Business Problems Into Intelligent Workflows', 'Intelligent workflows should begin with a business problem, a stable process, useful data, and clear human responsibility.', 'The strongest intelligent workflow projects connect business problems to product thinking. They define the job to be done, the human decision points, the data needed for support, and the guardrails for responsible use. Garcia Systems frames this work around turning business problems into products, systems, and intelligent workflows.', 'ai-readiness', ['ai-readiness', 'workflow-design', 'systems-strategy'], '2026-05-21'],
            ['What to Measure Before and After a Workflow Modernization Project', 'Useful modernization metrics focus on cycle time, rework, visibility, adoption, error rates, and decision quality.', 'Measurement should be part of workflow modernization from the beginning. Teams need a baseline for cycle time, rework, exception volume, and manual effort. After launch, adoption and decision quality matter as much as technical delivery. A small set of visible metrics helps keep the work grounded in business outcomes.', 'data-reporting', ['reporting', 'operations', 'systems-strategy'], '2026-06-04'],
        ];

        foreach ($articles as [$title, $excerpt, $body, $categorySlug, $tagSlugs, $date]) {
            $article = Article::create([
                'category_id' => $categories[$categorySlug]->id,
                'title' => $title,
                'slug' => Str::slug($title),
                'excerpt' => $excerpt,
                'body' => $body,
                'published_at' => Carbon::parse($date),
            ]);
            $article->tags()->sync(collect($tagSlugs)->map(fn ($slug) => $tags[$slug]->id));
        }

        $industries = collect(['Healthcare','Education','Logistics','Retail','Manufacturing','Government','Professional Services'])
            ->mapWithKeys(fn ($name) => [Str::slug($name) => Industry::create(['name' => $name, 'slug' => Str::slug($name), 'description' => 'Opportunity mapping context for '.$name.' organizations improving products, systems, and intelligent workflows.'])]);
        $companyTypes = collect(['Small business','Growing mid-market team','Multi-location operator','Public agency','Enterprise division','Professional practice','Regional service provider'])
            ->mapWithKeys(fn ($name) => [Str::slug($name) => CompanyType::create(['name' => $name, 'slug' => Str::slug($name), 'description' => 'Common organization type for Garcia Systems discovery.'])]);
        $departments = collect(['Operations','Sales','Finance','Customer Support','Procurement','Compliance','Human Resources','IT','Field Operations','Administration','Clinical Operations','Student Services'])
            ->mapWithKeys(fn ($name) => [Str::slug($name) => Department::create(['name' => $name, 'slug' => Str::slug($name), 'description' => 'Business function with recurring systems, workflow, and coordination work.'])]);

        $capabilities = collect(['Automation','Reporting','Data Quality','Workflow Visibility','Knowledge Management','Vendor Coordination','Systems Integration','Intake Management','Approval Routing','Records Management','Exception Handling','Operational Dashboards'])
            ->mapWithKeys(fn ($name) => [Str::slug($name) => Capability::create(['name' => $name, 'slug' => Str::slug($name), 'description' => $name.' capability for practical workflow modernization.'])]);
        $patterns = collect([
            ['Structured intake and routing', ['automation','workflow-visibility','intake-management']],
            ['Operational dashboard', ['reporting','workflow-visibility','operational-dashboards']],
            ['Shared knowledge base', ['knowledge-management','data-quality']],
            ['Vendor coordination hub', ['vendor-coordination','automation','workflow-visibility']],
            ['Data cleanup workflow', ['data-quality','reporting','records-management']],
            ['Approval rules and exception queue', ['approval-routing','exception-handling','automation']],
            ['System-of-record clarification', ['systems-integration','data-quality','records-management']],
            ['Cross-system status layer', ['systems-integration','workflow-visibility','operational-dashboards']],
        ])->mapWithKeys(function ($item) use ($capabilities) {
            [$name, $slugs] = $item;
            $pattern = SolutionPattern::create(['name' => $name, 'slug' => Str::slug($name), 'description' => $name.' pattern for reducing operational friction while preserving human ownership.']);
            $pattern->capabilities()->attach(collect($slugs)->map(fn ($slug) => $capabilities[$slug]->id));
            return [$pattern->slug => $pattern];
        });

        $examples = [
            ['Healthcare','Growing mid-market team','Clinical Operations','Patient intake follow-up','Customer intake bottlenecks','customer-intake-bottlenecks','Structured intake and routing'],
            ['Healthcare','Multi-location operator','Compliance','Records reconciliation','Records reconciliation','records-reconciliation','Data cleanup workflow'],
            ['Education','Public agency','Compliance','Grant documentation','Knowledge silos','knowledge-silos','Shared knowledge base'],
            ['Education','Growing mid-market team','Student Services','Student services handoffs','Disconnected systems','disconnected-systems','Cross-system status layer'],
            ['Logistics','Multi-location operator','Operations','Inventory coordination','Inventory visibility','inventory-visibility','Operational dashboard'],
            ['Logistics','Regional service provider','Field Operations','Delivery exception review','Approval delays','approval-delays','Approval rules and exception queue'],
            ['Retail','Small business','Procurement','Supplier replenishment','Vendor coordination','vendor-coordination','Vendor coordination hub'],
            ['Retail','Multi-location operator','Customer Support','Return request intake','Duplicate work','duplicate-work','Structured intake and routing'],
            ['Manufacturing','Enterprise division','Finance','Production reporting','Manual reporting','manual-reporting','Operational dashboard'],
            ['Manufacturing','Regional service provider','Operations','Quality issue tracking','Data quality','data-quality','Data cleanup workflow'],
            ['Government','Public agency','Customer Support','Permit request review','Legacy system dependency','legacy-system-dependency','System-of-record clarification'],
            ['Government','Public agency','Administration','Board packet preparation','Manual reporting','manual-reporting-government','Operational dashboard'],
            ['Professional Services','Professional practice','Sales','Client intake and qualification','Customer intake bottlenecks','customer-intake-bottlenecks-professional-services','Structured intake and routing'],
            ['Professional Services','Growing mid-market team','IT','Internal request triage','Disconnected systems','disconnected-systems-professional-services','Cross-system status layer'],
        ];

        foreach ($examples as [$industryName, $companyTypeName, $departmentName, $workflowName, $frictionName, $frictionSlug, $patternName]) {
            $workflow = Workflow::create([
                'industry_id' => $industries[Str::slug($industryName)]->id,
                'company_type_id' => $companyTypes[Str::slug($companyTypeName)]->id,
                'department_id' => $departments[Str::slug($departmentName)]->id,
                'name' => $workflowName,
                'slug' => Str::slug($workflowName),
                'description' => 'A practical '.$workflowName.' workflow where Garcia Systems can map friction, clarify requirements, and shape useful systems improvements.',
            ]);
            $friction = FrictionPoint::create([
                'workflow_id' => $workflow->id,
                'name' => $frictionName,
                'slug' => $frictionSlug,
                'description' => $frictionName.' creates delays, rework, and limited visibility for the team.',
                'impact' => 'Slower decisions, duplicated effort, avoidable handoffs, and harder coordination.',
            ]);
            $friction->solutionPatterns()->attach($patterns[Str::slug($patternName)]->id);
        }

        $assessmentQuestions = [
            ['Workflow documentation', 'Do you have clearly documented workflows?'],
            ['Data readiness', 'Is your operational data organized and accessible?'],
            ['Pilot selection', 'Can your team define measurable success for an AI or automation pilot?'],
            ['Stakeholder alignment', 'Do process owners have time to support implementation?'],
        ];

        foreach($assessmentQuestions as $i=>$question) AssessmentQuestion::create(['category'=>$question[0],'question'=>$question[1],'help_text'=>'Use your current operating reality, not an ideal future state.','sort_order'=>$i+1]);

        $videos = [
            ['Mapping Workflow Friction', 'A short placeholder summary for identifying repeated handoffs, manual reporting, and disconnected systems before proposing a product or automation path.'],
            ['AI Readiness in Plain English', 'A short placeholder summary for separating useful AI pilots from vague experimentation by checking data, workflow, risk, and ownership conditions.'],
            ['Inventory Visibility Walkthrough', 'A short placeholder summary for improving inventory visibility with focused dashboards, exception handling, and clearer update ownership.'],
            ['Records Reconciliation Patterns', 'A short placeholder summary for converting reconciliation work into an auditable workflow with matching rules, exception queues, and useful reporting.'],
            ['Vendor Coordination Hub Overview', 'A short placeholder summary for replacing scattered vendor email threads with structured requests, status visibility, and repeatable follow-up.'],
            ['Approval Delay Diagnostic', 'A short placeholder summary for finding where approvals stall because of missing context, unclear routing, or unnecessary escalation.'],
            ['Customer Intake Bottleneck Map', 'A short placeholder summary for mapping request channels, qualification steps, routing rules, and follow-up expectations.'],
            ['Knowledge Silos to Shared Systems', 'A short placeholder summary for connecting knowledge bases to real workflows so information stays useful and current.'],
        ];

        foreach ($videos as [$title, $description]) {
            Video::create([
                'title' => $title,
                'slug' => Str::slug($title),
                'url' => 'https://www.youtube.com/watch?v=ysz5S6PUM-U',
                'description' => $description,
                'is_published' => true,
            ]);
        }
    }
}
