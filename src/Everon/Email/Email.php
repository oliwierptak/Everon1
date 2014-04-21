<?php
/**
 * Created by PhpStorm.
 * User: Zeger
 * Date: 4/21/14
 * Time: 9:01 PM
 */

namespace Everon\Email;


class Email implements \Everon\Email\Interfaces\Email
{
    protected $headers;

    protected $subject;

    protected  $message;

    public function __construct($headers, $message, $subject)
    {
        $this->headers = $headers;
        $this->message = $message;
        $this->subject = $subject;
    }

    /**
     * @param mixed $headers
     */
    public function setHeaders($headers)
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
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
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


} 