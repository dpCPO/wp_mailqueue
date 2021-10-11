<?php

defined('TYPO3_MODE') || die();

call_user_func(static function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'WpMailqueue',
        'web', // Make module a submodule of 'web'
        'maillist', // Submodule key
        '', // Position
        [
            \WEBprofil\WpMailqueue\Controller\BackendController::class => 'list',
        ],
        [
            'access' => 'user,group',
            'icon'   => 'EXT:core/Resources/Public/Icons/T3Icons/svgs/module/module-generic.svg',
            'labels' => 'LLL:EXT:wp_mailqueue/Resources/Private/Language/locallang_maillist.xlf',
        ]
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('wp_mailqueue', 'Configuration/TypoScript', 'Mailqueue');

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_wpmailqueue_domain_model_mail', 'EXT:wp_mailqueue/Resources/Private/Language/locallang_csh_tx_wpmailqueue_domain_model_mail.xlf');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_wpmailqueue_domain_model_mail');
});
