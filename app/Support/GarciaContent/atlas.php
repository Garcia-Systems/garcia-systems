<?php

return [
    'industries' => [
        ['slug'=>'e-commerce','name'=>'E-commerce','description'=>'Retail and marketplace teams improving digital buying operations.'],
        ['slug'=>'public-health','name'=>'Public Health','description'=>'Public health organizations coordinating programs, reporting, and resident services.'],
    ],
    'company_types' => [
        ['slug'=>'direct-to-consumer-retailer','name'=>'Direct-to-consumer retailer','description'=>'Brands that sell and support customers through owned digital channels.'],
        ['slug'=>'public-health-agency','name'=>'Public health agency','description'=>'Government or nonprofit public health teams delivering community programs.'],
    ],
    'departments' => [
        ['slug'=>'commerce-operations','name'=>'Commerce Operations','description'=>'Teams responsible for orders, catalog quality, and customer operations.'],
        ['slug'=>'public-health-programs','name'=>'Public Health Programs','description'=>'Teams coordinating outreach, reporting, and service delivery.'],
    ],
    'capabilities' => [
        ['slug'=>'workflow-mapping','name'=>'Workflow mapping','description'=>'Document triggers, decisions, handoffs, and exceptions.'],
        ['slug'=>'data-quality-checks','name'=>'Data quality checks','description'=>'Detect missing, duplicate, stale, or inconsistent records.'],
        ['slug'=>'intake-triage','name'=>'Intake triage','description'=>'Route requests based on urgency, fit, and required follow-up.'],
        ['slug'=>'reporting-automation','name'=>'Reporting automation','description'=>'Turn operational data into repeatable metrics and dashboards.'],
        ['slug'=>'knowledge-retrieval','name'=>'Knowledge retrieval','description'=>'Help staff find policies, product facts, and approved guidance quickly.'],
        ['slug'=>'customer-communication','name'=>'Customer communication','description'=>'Improve timely, consistent, and contextual outbound updates.'],
    ],
    'solution_patterns' => [
        ['slug'=>'guided-intake','name'=>'Guided intake','description'=>'Structured forms and routing rules that capture complete requests.'],
        ['slug'=>'exception-dashboard','name'=>'Exception dashboard','description'=>'Operational views that surface stalled, risky, or incomplete work.'],
        ['slug'=>'content-and-knowledge-assistant','name'=>'Content and knowledge assistant','description'=>'Search and draft support backed by approved internal sources.'],
        ['slug'=>'data-readiness-pipeline','name'=>'Data readiness pipeline','description'=>'Validation and normalization before reporting or automation.'],
        ['slug'=>'program-reporting-pack','name'=>'Program reporting pack','description'=>'Repeatable metrics, exports, and narratives for stakeholders.'],
    ],
    'pattern_capabilities' => [
        'guided-intake' => ['workflow-mapping','intake-triage'],
        'exception-dashboard' => ['workflow-mapping','reporting-automation'],
        'content-and-knowledge-assistant' => ['knowledge-retrieval','customer-communication'],
        'data-readiness-pipeline' => ['data-quality-checks','reporting-automation'],
        'program-reporting-pack' => ['reporting-automation','data-quality-checks'],
    ],
    'workflows' => [
        ['slug'=>'ecommerce-return-exception-review','name'=>'Return exception review','industry'=>'e-commerce','company_type'=>'direct-to-consumer-retailer','department'=>'commerce-operations','description'=>'Review return requests that do not fit standard policy before issuing refunds or credits.','frictions'=>[['slug'=>'return-policy-edge-cases','name'=>'Return policy edge cases','description'=>'Support agents manually interpret exceptions across order history, product condition, and customer context.','impact'=>'Slow decisions and inconsistent customer experiences.','patterns'=>['guided-intake','exception-dashboard']]]],
        ['slug'=>'ecommerce-catalog-quality-review','name'=>'Catalog quality review','industry'=>'e-commerce','company_type'=>'direct-to-consumer-retailer','department'=>'commerce-operations','description'=>'Find product detail gaps before they create customer confusion or support volume.','frictions'=>[['slug'=>'incomplete-product-attributes','name'=>'Incomplete product attributes','description'=>'Missing size, compatibility, fulfillment, or policy fields are found after publication.','impact'=>'Higher returns and avoidable support tickets.','patterns'=>['data-readiness-pipeline']]]],
        ['slug'=>'ecommerce-promotion-launch-readiness','name'=>'Promotion launch readiness','industry'=>'e-commerce','company_type'=>'direct-to-consumer-retailer','department'=>'commerce-operations','description'=>'Coordinate campaign rules, inventory, pricing, and support guidance before launch.','frictions'=>[['slug'=>'promotion-handoff-gaps','name'=>'Promotion handoff gaps','description'=>'Marketing, operations, and support teams rely on scattered launch notes.','impact'=>'Incorrect discounts and delayed support responses.','patterns'=>['content-and-knowledge-assistant','exception-dashboard']]]],
        ['slug'=>'ecommerce-customer-support-triage','name'=>'Customer support triage','industry'=>'e-commerce','company_type'=>'direct-to-consumer-retailer','department'=>'commerce-operations','description'=>'Prioritize support cases by urgency, order state, and customer impact.','frictions'=>[['slug'=>'support-priority-ambiguity','name'=>'Support priority ambiguity','description'=>'Agents spend time deciding what needs escalation and what can follow a standard path.','impact'=>'Longer resolution times for high-impact issues.','patterns'=>['guided-intake']]]],
        ['slug'=>'ecommerce-inventory-exception-monitoring','name'=>'Inventory exception monitoring','industry'=>'e-commerce','company_type'=>'direct-to-consumer-retailer','department'=>'commerce-operations','description'=>'Spot fulfillment, oversell, and backorder risks before customers are affected.','frictions'=>[['slug'=>'late-inventory-risk-signals','name'=>'Late inventory risk signals','description'=>'Inventory issues surface after orders are delayed or cancelled.','impact'=>'Missed service levels and preventable refunds.','patterns'=>['exception-dashboard','data-readiness-pipeline']]]],
        ['slug'=>'ecommerce-voice-of-customer-synthesis','name'=>'Voice-of-customer synthesis','industry'=>'e-commerce','company_type'=>'direct-to-consumer-retailer','department'=>'commerce-operations','description'=>'Turn reviews, tickets, and survey themes into operational improvements.','frictions'=>[['slug'=>'scattered-customer-feedback','name'=>'Scattered customer feedback','description'=>'Themes remain buried in separate tools and are hard to connect to product or process fixes.','impact'=>'Teams miss recurring friction and revenue opportunities.','patterns'=>['content-and-knowledge-assistant','program-reporting-pack']]]],
        ['slug'=>'public-health-community-referral-intake','name'=>'Community referral intake','industry'=>'public-health','company_type'=>'public-health-agency','department'=>'public-health-programs','description'=>'Receive, qualify, and route community referrals to the right program or partner.','frictions'=>[['slug'=>'referral-routing-uncertainty','name'=>'Referral routing uncertainty','description'=>'Staff must interpret eligibility, geography, and urgency from incomplete referral notes.','impact'=>'Delayed service connection for residents.','patterns'=>['guided-intake']]]],
        ['slug'=>'public-health-outreach-list-preparation','name'=>'Outreach list preparation','industry'=>'public-health','company_type'=>'public-health-agency','department'=>'public-health-programs','description'=>'Prepare targeted outreach lists for clinics, campaigns, or program follow-up.','frictions'=>[['slug'=>'outreach-data-cleanup','name'=>'Outreach data cleanup','description'=>'Teams manually clean duplicates, outdated contact fields, and eligibility flags.','impact'=>'Lower reach and inefficient staff time.','patterns'=>['data-readiness-pipeline']]]],
        ['slug'=>'public-health-case-follow-up','name'=>'Case follow-up coordination','industry'=>'public-health','company_type'=>'public-health-agency','department'=>'public-health-programs','description'=>'Coordinate follow-up tasks, documentation, and partner handoffs after intake.','frictions'=>[['slug'=>'case-follow-up-blind-spots','name'=>'Case follow-up blind spots','description'=>'Open tasks and exceptions are tracked in spreadsheets or inboxes.','impact'=>'Missed follow-ups and weak accountability.','patterns'=>['exception-dashboard']]]],
        ['slug'=>'public-health-guidance-response','name'=>'Guidance response support','industry'=>'public-health','company_type'=>'public-health-agency','department'=>'public-health-programs','description'=>'Help staff answer common public questions with approved, current guidance.','frictions'=>[['slug'=>'guidance-version-confusion','name'=>'Guidance version confusion','description'=>'Staff search multiple documents to confirm the latest approved answer.','impact'=>'Inconsistent public communication.','patterns'=>['content-and-knowledge-assistant']]]],
        ['slug'=>'public-health-grant-reporting','name'=>'Grant reporting','industry'=>'public-health','company_type'=>'public-health-agency','department'=>'public-health-programs','description'=>'Compile program outputs and narratives for funders and leadership.','frictions'=>[['slug'=>'manual-grant-metric-assembly','name'=>'Manual grant metric assembly','description'=>'Required metrics are assembled repeatedly from source systems and spreadsheets.','impact'=>'Reporting consumes time that could support program delivery.','patterns'=>['program-reporting-pack']]]],
        ['slug'=>'public-health-clinic-capacity-planning','name'=>'Clinic capacity planning','industry'=>'public-health','company_type'=>'public-health-agency','department'=>'public-health-programs','description'=>'Coordinate appointment demand, staffing, supplies, and partner referrals.','frictions'=>[['slug'=>'clinic-capacity-signal-gaps','name'=>'Clinic capacity signal gaps','description'=>'Demand, staffing, and supply signals are reviewed too late for proactive adjustments.','impact'=>'Longer waits and avoidable underuse of capacity.','patterns'=>['exception-dashboard','program-reporting-pack']]]],
    ],
];
