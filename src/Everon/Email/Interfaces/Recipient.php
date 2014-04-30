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
     * @param array $cc
     */
    public function setCc(array $cc);

    /**
     * @param array $bcc
     */
    public function setBcc(array $bcc);

    /**
     * @return string
     */
    public function getTo();

    /**
     * @return array
     */
    public function getBcc();

    /**
     * @param string $to
     */
    public function setTo($to);

    /**
     * @return array
     */
    public function getCc();
}