<?php
/**
 * Created by PhpStorm.
 * User: Zeger
 * Date: 4/21/14
 * Time: 9:04 PM
 */
namespace Everon\Email\Interfaces;

interface Email
{
    /**
     * @param mixed $headers
     */
    public function setHeaders($headers);

    /**
     * @param mixed $message
     */
    public function setMessage($message);

    /**
     * @return mixed
     */
    public function getSubject();

    /**
     * @return mixed
     */
    public function getMessage();

    /**
     * @return mixed
     */
    public function getHeaders();

    /**
     * @param mixed $subject
     */
    public function setSubject($subject);
}