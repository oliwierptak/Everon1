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
interface Message
{
    /**
     * @param array $headers
     */
    function setHeaders(array $headers);

    /**
     * @return string
     */
    function getSubject();

    /**
     * @return array
     */
    function getHeaders();

    /**
     * @param string $subject
     */
    function setSubject($subject);

    /**
     * @param array $attachments
     */
    function setAttachments($attachments);

    /**
     * @return array
     */
    function getAttachments();

    /**
     * @param Recipient $Recipient
     */
    function setRecipient(Recipient $Recipient);

    /**
     * @return Recipient
     */
    function getRecipient();

    /**
     * @param $fromEmail
     * @return mixed
     */
    function setFromEmail($fromEmail);

    /**
     * @return mixed
     */
    function getFromEmail();
    /**
     * @param mixed $fromName
     */
    function setFromName($fromName);

    /**
     * @return mixed
     */
    function getFromName();

    /**
     * @param mixed $plainBody
     */
    function setPlainBody($plainBody);

    /**
     * @return mixed
     */
    function getPlainBody();

    /**
     * @param mixed $richBody
     */
    function setRichBody($richBody);

    /**
     * @return mixed
     */
    function getRichBody();

}