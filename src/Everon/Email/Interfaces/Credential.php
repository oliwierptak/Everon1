<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Email\Interfaces;

/**
 * @author Zeger Hoogeboom <zeger_hoogeboom@hotmail.com>
 * @author Oliwier Ptak <oliwierptak@gmail.com>
 */
interface Credential
{
    /**
     * @return string
     */
    function getPassword();

    /**
     * @return string
     */
    function getUsername();

    /**
     * @return int
     */
    function getPort();

    /**
     * @param string $password
     */
    function setPassword($password);

    /**
     * @return string
     */
    function getEmail();

    /**
     * @param string $username
     */
    function setUsername($username);

    /**
     * @param string $senderName
     */
    function setName($senderName);

    /**
     * @return string
     */
    function getName();

    /**
     * @param string $fromEmail
     */
    function setEmail($fromEmail);

    /**
     * @param string $port
     */
    function setPort($port);

    /**
     * @param string $host
     */
    function setHost($host);

    /**
     * @return string
     */
    function getHost();
}