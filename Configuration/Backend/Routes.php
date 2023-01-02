<?php

return [
    'delete' => [
        'path' => '/delete',
        'target' => \WEBprofil\WpMailqueue\Controller\BackendController::class . '::deleteAction'
    ]
];
