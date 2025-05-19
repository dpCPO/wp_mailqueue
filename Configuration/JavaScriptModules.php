<?php
return [
    'dependencies' => ['core', 'backend'],
    'imports' => [
    		'@typo3/backend/event/page-loaded' => 'EXT:backend/Resources/Public/JavaScript/event/page-loaded.js',
    		'@webprofil/wp-mailqueue/' => 'EXT:wp_mailqueue/Resources/Public/JavaScript/',
    ],
];

?>