<?php

return [
    'features' => [
        'ai_assessment' => env('FEATURE_AI_ASSESSMENT', true),
        'opportunity_atlas' => env('FEATURE_OPPORTUNITY_ATLAS', true),
        'opportunity_explorer' => env('FEATURE_OPPORTUNITY_EXPLORER', env('FEATURE_OPPORTUNITY_ATLAS', true)),
    ],
];
