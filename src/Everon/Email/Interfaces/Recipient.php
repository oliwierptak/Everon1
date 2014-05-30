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
interface Recipient extends \Everon\Interfaces\Arrayable
{

    /**
     * @return array
     */
    function getCc();

    /**
     * @param array $cc
     */
    function setCc(array $cc);

    /**
     * @return array
     */
    function getBcc();

    /**
     * @param array $bcc
     */
    function setBcc(array $bcc);

    /**
     * @return array
     */
    function getTo();
    /**
     * @param array $to
     */
    function setTo(array $to);
}