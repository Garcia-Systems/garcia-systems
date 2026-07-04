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
        foreach(['Healthcare services','Professional services','Field operations'] as $n) Industry::create(['name'=>$n,'slug'=>Str::slug($n),'description'=>'Sample industry for opportunity mapping.']);
        foreach(['Small business','Growing mid-market team','Multi-location operator'] as $n) CompanyType::create(['name'=>$n,'slug'=>Str::slug($n),'description'=>'Common organization type for Garcia Systems discovery.']);
        foreach(['Operations','Sales','Finance'] as $n) Department::create(['name'=>$n,'slug'=>Str::slug($n),'description'=>'Business function with recurring systems work.']);
        $cap=Capability::create(['name'=>'Workflow automation','slug'=>'workflow-automation','description'=>'Automate steps, reminders, data movement, and reporting around an existing process.']);
        $solution=SolutionPattern::create(['name'=>'Structured intake and routing','slug'=>'structured-intake-routing','description'=>'Replace ad hoc requests with a simple intake, triage, routing, and status workflow.']); $solution->capabilities()->attach($cap);
        $industry=Industry::first(); $dept=Department::first();
        $workflow=Workflow::create(['industry_id'=>$industry->id,'department_id'=>$dept->id,'name'=>'Client intake and follow-up','slug'=>'client-intake-follow-up','description'=>'Capture requests, qualify needs, route work, and follow up consistently.']);
        $fp=FrictionPoint::create(['workflow_id'=>$workflow->id,'name'=>'Manual status chasing','slug'=>'manual-status-chasing','description'=>'Team members spend time asking where requests stand instead of moving work forward.','impact'=>'Slower response times and lower visibility.']); $fp->solutionPatterns()->attach($solution);
        foreach(['Do you have clearly documented workflows?','Is your operational data organized and accessible?','Can your team define measurable success for an AI or automation pilot?','Do process owners have time to support implementation?'] as $i=>$q) AssessmentQuestion::create(['question'=>$q,'help_text'=>'Use your current operating reality, not an ideal future state.','sort_order'=>$i+1]);
        foreach(['Mapping Workflow Friction','AI Readiness in Plain English'] as $title) Video::create(['title'=>$title,'slug'=>Str::slug($title),'url'=>'https://example.com/videos/'.Str::slug($title),'description'=>'Placeholder video entry for a short Garcia Systems explainer.']);
    }
}
