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

    protected $subject;

    protected $body;

    /**
     * @var array
     */
    protected $attachments;

    /**
     * @var Interfaces\Recipient
     */
    protected $Recipient;

    public function __construct(Interfaces\Recipient $Recipient, $subject, $body, array $attachments=[],array $headers=[])
    {
        $this->Recipient = $Recipient;
        $this->subject = $subject;
        $this->body = $body;
        $this->headers = $headers;
        $this->attachments = $attachments;
    }

    /**
     * @param array  $headers
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * @return mixed
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param mixed $message
     */
    public function setBody($message)
    {
        $this->body = $message;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param array $attachments
     */
    public function setAttachments($attachments)
    {
        $this->attachments = $attachments;
    }

    /**
     * @return array
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * @param \Everon\Email\Interfaces\Recipient $Recipient
     */
    public function setRecipient(Interfaces\Recipient $Recipient)
    {
        $this->Recipient = $Recipient;
    }

    /**
     * @return \Everon\Email\Interfaces\Recipient
     */
    public function getRecipient()
    {
        return $this->Recipient;
    }
    
}