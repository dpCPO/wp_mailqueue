<?php

return [
    'wpmailqueue_maillist' => [
        'parent' => 'web',
        'position' => [],
        'access' => 'user,group',
        'workspaces' => 'live',
        'iconIdentifier' => 'module-generic',
        'path' => '/module/system/wpmailqueue_maillist',
        'labels' => 'LLL:EXT:wp_mailqueue/Resources/Private/Language/locallang_maillist.xlf',
        'extensionName' => 'WpMailqueue',
        'controllerActions' => [
            \WEBprofil\WpMailqueue\Controller\BackendController::class => [
                'list',
            ],
        ],
    ],
];
