<?php
/**
 * Created by PhpStorm.
 * User: Zeger
 * Date: 28/04/14
 * Time: 11:31
 */
namespace Everon\Email\Interfaces;

interface Recipient
{
    /**
     * @param mixed $cc
     */
    public function setCc($cc);

    /**
     * @param mixed $bcc
     */
    public function setBcc($bcc);

    /**
     * @return mixed
     */
    public function getTo();

    /**
     * @return mixed
     */
    public function getBcc();

    /**
     * @param mixed $to
     */
    public function setTo($to);

    /**
     * @return mixed
     */
    public function getCc();
}