<?php

return [
    'trueprogramming-instagram' => [
        'parent' => 'web',
        'access' => 'user',
        'workspaces' => 'live',
        'path' => '/module/tp-instagram',
        'labels' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_mod.xlf',
        'extensionName' => 'Instagram',
        'iconIdentifier' => 'instagram-module',
        'inheritNavigationComponentFromMainModule' => false,
        'controllerActions' => [
            \Trueprogramming\Instagram\Controller\DashboardController::class => [
                'show',
                'importFeed',
                'revokeToken',
            ],
        ],
    ],
];
