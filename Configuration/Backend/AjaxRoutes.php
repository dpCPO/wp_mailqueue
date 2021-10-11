<?php

use WEBprofil\WpMailqueue\Controller\BackendController;
return [
    'wp_mails' => [
        'path' => '/wp/mails',
        'target' => BackendController::class . '::getMailsAsJson'
    ]
];
