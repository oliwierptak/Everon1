<?php
namespace Everon\Email\Interfaces;

interface Credential
{
    /**
     * @return mixed
     */
    public function getPassword();

    /**
     * @return mixed
     */
    public function getUsername();

    /**
     * @return mixed
     */
    public function getPort();

    /**
     * @param mixed $password
     */
    public function setPassword($password);

    /**
     * @return mixed
     */
    public function getSenderEmail();

    /**
     * @param mixed $username
     */
    public function setUsername($username);

    /**
     * @param mixed $senderName
     */
    public function setSenderName($senderName);

    /**
     * @return mixed
     */
    public function getSenderName();

    /**
     * @param mixed $fromEmail
     */
    public function setSenderEmail($fromEmail);

    /**
     * @param mixed $port
     */
    public function setPort($port);

    /**
     * @param mixed $server
     */
    public function setServer($server);

    /**
     * @return mixed
     */
    public function getServer();
}