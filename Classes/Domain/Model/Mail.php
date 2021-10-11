<?php

declare(strict_types=1);

namespace WEBprofil\WpMailqueue\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Mail\FluidEmail;

/**
 * This file is part of the "Mailqueue" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2021 Nikita Hovratov <entwicklung@nikita-hovratov.de>
 */

/**
 * Mail
 */
class Mail extends AbstractEntity
{

    /**
     * subject
     *
     * @var string
     */
    protected $subject = '';

    /**
     * body
     *
     * @var string
     */
    protected $body = '';

    /**
     * @var string
     */
    protected $bodyHtml = '';

    /**
     * sender
     *
     * @var Address
     */
    protected $sender;

    /**
     * @var Address
     */
    protected $recipient;

    /**
     * cc
     *
     * @var array
     */
    protected $cc = [];

    /**
     * bcc
     *
     * @var array
     */
    protected $bcc = [];

    /**
     * attachements
     *
     * @var array
     */
    protected $attachements = [];

    /**
     * dateSent
     *
     * @var int
     */
    protected $dateSent = 0;

    /**
     * type
     *
     * @var string
     */
    protected $type = FluidEmail::FORMAT_PLAIN;

    /**
     * Returns the subject
     *
     * @return string $subject
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Sets the subject
     *
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Returns the body
     *
     * @return string $body
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Sets the body
     *
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Returns the sender
     *
     * @return string $sender
     */
    public function getSender()
    {
        return implode(';', [$this->sender->getAddress(), $this->sender->getName()]);
    }

    public function getSenderAddress()
    {
        return $this->sender;
    }

    /**
     * Sets the sender
     *
     * @param Address $sender
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    public function setSenderString(string $sender)
    {
        $parts = explode(';', $sender);
        $this->sender = new Address($parts[0], $parts[1]);
    }

    /**
     * Returns the dateSent
     *
     * @return int $dateSent
     */
    public function getDateSent()
    {
        return $this->dateSent;
    }

    /**
     * Sets the dateSent
     *
     * @param int $dateSent
     */
    public function setDateSent($dateSent)
    {
        $this->dateSent = $dateSent;
    }

    /**
     * Returns the type
     *
     * @return string $type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the type
     *
     * @param string $type
     */
    public function setType(string $type)
    {
        if (!in_array($type, [FluidEmail::FORMAT_PLAIN, FluidEmail::FORMAT_HTML, FluidEmail::FORMAT_BOTH])) {
            throw new \InvalidArgumentException('Setting Mail->setType() must be either "html", "plain" or "both", no other formats are currently supported', 1580743847);
        }
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getCc(): string
    {
        $ccArray = [];
        foreach ($this->cc as $cc) {
            $ccArray[] = implode(';', [$cc->getAddress(), $cc->getName()]);
        }
        return implode(',', $ccArray);
    }

    public function getCcAddresses()
    {
        return $this->cc;
    }

    /**
     * @param array $cc
     */
    public function setCc(array $cc): void
    {
        $this->cc = $cc;
    }

    public function addCc(Address $cc)
    {
        $this->cc[] = $cc;
    }

    public function setCcString(string $cc)
    {
        if ($cc !== '') {
            $ccArray = [];
            $ccList = explode(',', $cc);
            foreach ($ccList as $singleCc) {
                $ccParts = explode(';', $singleCc);
                $ccArray[] = new Address($ccParts[0], $ccParts[1]);
            }
            $this->cc = $ccArray;
        }
    }

    /**
     * @return string
     */
    public function getBcc(): string
    {
        $bccArray = [];
        foreach ($this->bcc as $bcc) {
            $bccArray[] = implode(';', [$bcc->getAddress(), $bcc->getName()]);
        }
        return implode(',', $bccArray);
    }

    public function getBccAddresses()
    {
        return $this->bcc;
    }

    /**
     * @param array $bcc
     */
    public function setBcc(array $bcc): void
    {
        $this->bcc = $bcc;
    }

    public function addBcc(Address $bcc)
    {
        $this->bcc[] = $bcc;
    }

    public function setBccString(string $bcc)
    {
        if ($bcc !== '') {
            $bccList = explode(',', $bcc);
            $bccArray = [];
            foreach ($bccList as $singleBcc) {
                $bccParts = explode(';', $singleBcc);
                $bccArray[] = new Address($bccParts[0], $bccParts[1]);
            }
            $this->bcc = $bccArray;
        }
    }

    /**
     * @return string
     */
    public function getAttachements(): string
    {
        return implode(',', $this->attachements);
    }

    public function getAttachementsArray()
    {
        return $this->attachements;
    }

    /**
     * @param array $attachements
     */
    public function setAttachements(array $attachements): void
    {
        $this->attachements = $attachements;
    }

    public function addAttachement(string $attachement)
    {
        $this->attachements[] = $attachement;
    }

    public function setAttachementsString(string $attachements)
    {
        if ($attachements !== '') {
            $this->attachements = explode(',', $attachements);
        }
    }

    /**
     * @return string
     */
    public function getRecipient(): string
    {
        return implode(';', [$this->recipient->getAddress(), $this->recipient->getName()]);
    }

    public function getRecipientAddress()
    {
        return $this->recipient;
    }

    /**
     * @param Address $recipient
     */
    public function setRecipient(Address $recipient): void
    {
        $this->recipient = $recipient;
    }

    public function setRecipientString(string $recipient)
    {
        $parts = explode(';', $recipient);
        $this->recipient = new Address($parts[0], $parts[1]);
    }

    /**
     * @return string
     */
    public function getBodyHtml(): string
    {
        return $this->bodyHtml;
    }

    /**
     * @param string $bodyHtml
     */
    public function setBodyHtml(string $bodyHtml): void
    {
        $this->bodyHtml = $bodyHtml;
    }
}
