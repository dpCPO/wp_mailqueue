<?php /** @noinspection ALL */

defined('TYPO3') || die();

call_user_func(static function () {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('wp_mailqueue', 'Configuration/TypoScript', 'Mailqueue');
});
