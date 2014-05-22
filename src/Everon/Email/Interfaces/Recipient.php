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
interface Recipient
{
    /**
     * @param array $cc
     */
    function setCc(array $cc);

    /**
     * @param array $bcc
     */
    function setBcc(array $bcc);

    /**
     * @return string
     */
    function getTo();

    /**
     * @return array
     */
    function getBcc();

    /**
     * @param string $to
     */
    function setTo($to);

    /**
     * @return array
     */
    function getCc();

    /**
     * @param string $name
     */
    function setName($name);

    /**
     * @return string
     */
    function getName();
}