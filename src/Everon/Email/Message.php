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

use Everon\Helper;

/**
 * @author Zeger Hoogeboom <zeger_hoogeboom@hotmail.com>
 * @author Oliwier Ptak <oliwierptak@gmail.com>
 */
class Message implements Interfaces\Message
{
    use Helper\ToArray;

    /**
     * @var Interfaces\Recipient
     */
    protected $Recipient;

    /**
     * @var Interfaces\Address
     */
    protected $From = null;

    /**
     * @var string
     */
    protected $subject = null;

    /**
     * @var string
     */
    protected $html_body = null;

    /**
     * @var string
     */
    protected $text_body = null;

    /**
     * @var array
     */
    protected $attachments;
    
    /**
     * @var array
     */
    protected $headers = null;


    
    public function __construct(Interfaces\Recipient $Recipient, Interfaces\Address $FromAddress, $subject, $html_body, $text_body='', array $attachments=[], array $headers=[])
    {
        $this->Recipient = $Recipient;
        $this->From = $FromAddress;
        $this->subject = $subject;
        $this->html_body = $html_body;
        $this->text_body = $text_body;
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
    public function setTextBody($text_body)
    {
        $this->text_body = $text_body;
    }

    /**
     * @inheritdoc
     */
    public function getTextBody()
    {
        return $this->text_body;
    }

    /**
     * @inheritdoc
     */
    public function setHtmlBody($richBody)
    {
        $this->html_body = $richBody;
    }

    /**
     * @inheritdoc
     */
    public function getHtmlBody()
    {
        return $this->html_body;
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
    public function setFrom(Interfaces\Address $From)
    {
        $this->From = $From;
    }

    /**
     * @inheritdoc
     */
    public function getFrom()
    {
        return $this->From;
    }
    
    public function getToArray()
    {
        return [
            'recipient' => $this->getRecipient()->toArray(),
            'from' => $this->getFrom()->toArray(),
            'subject' => $this->getSubject(),
            'html_body' => $this->getHtmlBody(),
            'text_body' => $this->getTextBody(),
            'attachments' => (new Helper\Collection($this->getAttachments()))->toArray(true),
            'headers' => (new Helper\Collection($this->getHeaders()))->toArray(true)
        ];
    }

}
