<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Email;

/**
 * @author Zeger Hoogeboom <zeger_hoogeboom@hotmail.com>
 * @author Oliwier Ptak <oliwierptak@gmail.com>
 */
class Message implements Interfaces\Message
{
    /**
     * @var array
     */
    protected $headers;

    protected $fromEmail;

    protected $fromName;

    protected $subject;

    protected $richBody;

    protected $plainBody;

    /**
     * @var array
     */
    protected $attachments;

    /**
     * @var Interfaces\Recipient
     */
    protected $Recipient;

    public function __construct(Interfaces\Recipient $Recipient, $fromEmail, $fromName, $subject, $richBody, $plainBody, array $attachments=[],array $headers=[])
    {
        $this->Recipient = $Recipient;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
        $this->subject = $subject;
        $this->richBody = $richBody;
        $this->plainBody = $plainBody;
        $this->headers = $headers;
        $this->attachments = $attachments;
    }

    /**
     * @inheritdoc
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * @inheritdoc
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @inheritdoc
     */
    public function setPlainBody($plainBody)
    {
        $this->plainBody = $plainBody;
    }

    /**
     * @inheritdoc
     */
    public function getPlainBody()
    {
        return $this->plainBody;
    }

    /**
     * @inheritdoc
     */
    public function setRichBody($richBody)
    {
        $this->richBody = $richBody;
    }

    /**
     * @inheritdoc
     */
    public function getRichBody()
    {
        return $this->richBody;
    }

    /**
     * @inheritdoc
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @inheritdoc
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @inheritdoc
     */
    public function setAttachments($attachments)
    {
        $this->attachments = $attachments;
    }

    /**
     * @inheritdoc
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * @inheritdoc
     */
    public function setRecipient(Interfaces\Recipient $Recipient)
    {
        $this->Recipient = $Recipient;
    }

    /**
     * @inheritdoc
     */
    public function getRecipient()
    {
        return $this->Recipient;
    }

    /**
     * @inheritdoc
     */
    public function setFromEmail($fromEmail)
    {
        $this->fromEmail = $fromEmail;
    }

    /**
     * @inheritdoc
     */
    public function getFromEmail()
    {
        return $this->fromEmail;
    }

    /**
     * @inheritdoc
     */
    public function setFromName($fromName)
    {
        $this->fromName = $fromName;
    }

    /**
     * @inheritdoc
     */
    public function getFromName()
    {
        return $this->fromName;
    }

}
