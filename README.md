# TYPO3 extension `wp_mailqueue`

This extension offers the possibility, to send mails from your extbase extension via a Mailqueue.
Usually, when you send mails via Extbase, the Mails are sent immediately. This can run into problems with your mailprovider, when he allows only a specific amount of mails per time.
This extensions help you with this problem.

## How does it work
Instead of sendin Mails like this:

```php
$mail = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Mail\MailMessage::class);
$mail
   ->from(new \Symfony\Component\Mime\Address('john.doe@example.org', 'John Doe'))
   ->to(
      new \Symfony\Component\Mime\Address('receiver@example.com', 'Max Mustermann'),
      new \Symfony\Component\Mime\Address('other@example.net')
   )
   ->subject('Your subject')
   ->text('Here is the message itself')
   ->send()
 ;
```
 
Use our Class/Method: 
```php
$mail = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\WEBprofil\WpMailqueue\Domain\Model\Mail::class);
$mail->setSender(new Address($this->settings['fromMail'], $this->settings['fromName']));
$mail->setRecipient(new Address($recipient));
$mail->setSubject(LocalizationUtility::translate('mail_subject', 'my_extkey', null, $language));
$mail->setBody($html);
$mail->addAttachement($fileObject->getCombinedIdentifier());
MailqueueUtility::addToMailqueue($mail);
```

## Full control in the Backend
Take a look into the Backend module of wp_mailqueue. It is self explained. Here you can have a look to the queued and sent mails.
You can delete queued mails.

## Scheduler Taks for sending
Add and activate the Scheduler Task to activate the Mailsending from the mailqueue:
Execute console commands
mailqueue:run
