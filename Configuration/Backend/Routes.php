<?php

use WEBprofil\WpMailqueue\Controller\BackendController;
return [
    'delete' => [
        'path' => '/delete',
        'target' => BackendController::class . '::deleteAction'
    ]
];
