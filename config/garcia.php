<?php

return [
    'features' => [
        'ai_assessment' => env('FEATURE_AI_ASSESSMENT', true),
        'opportunity_atlas' => env('FEATURE_OPPORTUNITY_ATLAS', true),
        'opportunity_explorer' => env('FEATURE_OPPORTUNITY_EXPLORER', env('FEATURE_OPPORTUNITY_ATLAS', true)),
    ],
    'featured_laboratories' => [
        [
            'name' => 'Opportunity Atlas Laboratory',
            'context' => 'Operational discovery · relational system map',
            'description' => 'An executable map that connects industries and workflows to observable friction and practical solution patterns.',
            'route' => 'atlas',
            'cta' => 'Explore laboratory',
            'diagram' => 'atlas',
        ],
        [
            'name' => 'AI Readiness Decision Instrument',
            'context' => 'Decision support · scored assessment',
            'description' => 'A working assessment that turns operating evidence into category scores, readiness tiers, risks, and next steps.',
            'route' => 'assessment',
            'cta' => 'Run assessment',
            'diagram' => 'readiness',
        ],
        [
            'name' => 'Knowledge Publishing System',
            'context' => 'Structured content · public knowledge',
            'description' => 'A publishing workflow that moves reviewed systems thinking into searchable, accessible public guidance.',
            'route' => 'articles.index',
            'cta' => 'View published work',
            'diagram' => 'publishing',
        ],
    ],
];
