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
 */
interface Sender
{

    /**
     * @param Message $Message
     * @return bool
     */
    function send(Message $Message);

    /**
     * @param Credential $Credential
     */
    function setCredential(Credential $Credential);

    /**
     * @return Credential
     */
    function getCredential();
}