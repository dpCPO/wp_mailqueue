<?php

namespace WEBprofil\WpMailqueue\Utility;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WEBprofil\WpMailqueue\Domain\Model\Mail;
use Doctrine\DBAL\Query\QueryBuilder;

class MailqueueUtility {
	
	protected static $table = 'tx_wpmailqueue_domain_model_mail';
	
	public static function addToMailqueue(Mail $mail) {
		
		$storagePid = 3;
// 		try {
// 			$configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
// 			$settings = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK, 'WpMailqueue');
			
// 			$storagePid = (int)($settings['persistence']['storagePid'] ?? 0);
// 		}
// 		catch( \Throwable $throwable ){	}
		
		/** @var ConnectionPool $connectionPool */
		$connectionPool = GeneralUtility::makeInstance( ConnectionPool::class );
		
		/** @var QueryBuilder $queryBuilder */
		$queryBuilder = $connectionPool->getQueryBuilderForTable( self::$table );
		
		$queryBuilder->insert( self::$table )
			->values([ 
				'pid' => $storagePid,
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
		])->executeStatement();
	}
}
