<?php

namespace WEBprofil\WpMailqueue\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mime\Email;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\Mailer;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WEBprofil\WpMailqueue\Domain\Model\Mail;

class MailqueueCommand extends Command
{
    public function configure()
    {
        $this->setDescription('Sends the e-mails in the mailqueue.');
        $this->addArgument('limit', InputArgument::OPTIONAL, 'Define the maximum number of mails to send per run.', 10);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());

        $io->writeln('Start mailqueue...');

        $queryBuilder = $this->getQueryBuilder();
        $mails = $queryBuilder
            ->select('*')
            ->from('tx_wpmailqueue_domain_model_mail')
            ->where($queryBuilder->expr()->eq('date_sent', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)))
            ->orderBy('crdate')
            ->setMaxResults((int)$input->getArgument('limit'))
            ->execute();

        while ($mail = $mails->fetch()) {
            $mailModel = new Mail();
            $mailModel->setSenderString($mail['sender']);
            $mailModel->setRecipientString($mail['recipient']);
            $mailModel->setSubject($mail['subject']);
            $mailModel->setBody($mail['body']);
            $mailModel->setBodyHtml($mail['body_html']);
            $mailModel->setCcString($mail['cc']);
            $mailModel->setBccString($mail['bcc']);
            $mailModel->setType($mail['type']);
            $mailModel->setAttachementsString($mail['attachements']);

            $success = $this->sendMail($mailModel);

            if ($success) {
                $queryBuilder = $this->getQueryBuilder();
                $queryBuilder
                    ->update('tx_wpmailqueue_domain_model_mail')
                    ->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($mail['uid'], \PDO::PARAM_INT)))
                    ->set('date_sent', time())
                    ->execute();
            }
        }

        return Command::SUCCESS;
    }

    protected function getQueryBuilder()
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_wpmailqueue_domain_model_mail');
    }

    protected function sendMail(Mail $mailModel)
    {
        /** @var Email $mail */
        $mail = GeneralUtility::makeInstance(Email::class);
        $mail
            ->from($mailModel->getSenderAddress())
            ->to($mailModel->getRecipientAddress())
            ->subject($mailModel->getSubject());

        if (in_array($mailModel->getType(), [FluidEmail::FORMAT_PLAIN, FluidEmail::FORMAT_BOTH])) {
            $mail->text($mailModel->getBody());
        }

        if (in_array($mailModel->getType(), [FluidEmail::FORMAT_HTML, FluidEmail::FORMAT_BOTH])) {
            $mail->html($mailModel->getBodyHtml());
        }

        foreach ($mailModel->getAttachementsArray() as $attachement) {
        	// Check wether we use TYPO3 Storage or simple file attachments
        	// ATTENTION: Works only with a good os.
        	if( is_string($attachement) && str_starts_with($attachement, "/") ){
        		$mail->attachFromPath( $attachement );
        	}
        	else {
            $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
            $fileObject = $resourceFactory->getFileObjectFromCombinedIdentifier($attachement);
            $fullPath = $fileObject->getStorage()->getConfiguration()['basePath'] . substr($fileObject->getIdentifier(), 1);
            $mail->attachFromPath(Environment::getPublicPath() . '/' . $fullPath);
        	}
        }

        if ($mailModel->getCc()) {
            $mail->cc(...$mailModel->getCcAddresses());
        }

        if ($mailModel->getBccAddresses()) {
            $mail->bcc(...$mailModel->getBccAddresses());
        }

        $mailer = GeneralUtility::makeInstance(Mailer::class);
        $mailer->send($mail);
        return $mailer->getSentMessage() !== null;
    }
}

