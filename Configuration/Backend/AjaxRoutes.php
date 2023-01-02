<?php

return [
    'wp_mails' => [
        'path' => '/wp/mails',
        'target' => \WEBprofil\WpMailqueue\Controller\BackendController::class . '::getMailsAsJson'
    ]
];
