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