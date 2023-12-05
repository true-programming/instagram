<?php

declare(strict_types=1);

return [
    'frontend' => [
        'trueprogramming/instagram' => [
            'target' => \Trueprogramming\Instagram\Middleware\GetInstagramAuthenticationCode::class,
            'before' => [
                'typo3/cms-frontend/timetracker',
            ],
        ],
    ],
];
