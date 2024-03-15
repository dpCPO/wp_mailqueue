<?php

namespace WEBprofil\WpMailqueue\Utility;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use WEBprofil\WpMailqueue\Domain\Model\Mail;

class MailqueueUtility
{
    protected static $table = 'tx_wpmailqueue_domain_model_mail';

    public static function addToMailqueue(Mail $mail)
    {
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $settings = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK, 'WpMailqueue');

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::$table);

        $queryBuilder
            ->insert(self::$table)
            ->values(
                [
                    'pid' => (int)($settings['persistence']['storagePid'] ?? 0),
                    'crdate' => time(),
                    'tstamp' => time(),
                    'sender' => $mail->getSender(),
                    'subject' => $mail->getSubject(),
                    'body' => $mail->getBody(),
                    'body_html' => $mail->getBodyHtml(),
                    'recipient' => $mail->getRecipient(),
                    'cc' => $mail->getCc(),
                    'bcc' => $mail->getBcc(),
                    'attachements' => $mail->getAttachements(),
                    'type' => $mail->getType()
                ]
            )
            ->executeStatement();
    }
}
