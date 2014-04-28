<?php
namespace Everon\Email;


class Recipient implements \Everon\Email\Interfaces\Recipient
{

    protected $to;

    protected $cc;

    protected $bcc;

    function __construct($bcc, $cc, $to)
    {
        $this->bcc = $bcc;
        $this->cc = $cc;
        $this->to = $to;
    }

    /**
     * @param mixed $bcc
     */
    public function setBcc($bcc)
    {
        $this->bcc = $bcc;
    }

    /**
     * @return mixed
     */
    public function getBcc()
    {
        return $this->bcc;
    }

    /**
     * @param mixed $cc
     */
    public function setCc($cc)
    {
        $this->cc = $cc;
    }

    /**
     * @return mixed
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * @param mixed $to
     */
    public function setTo($to)
    {
        $this->to = $to;
    }

    /**
     * @return mixed
     */
    public function getTo()
    {
        return $this->to;
    }


} 