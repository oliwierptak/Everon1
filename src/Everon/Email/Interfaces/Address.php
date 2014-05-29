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

interface Address
{
    /**
     * @return string
     */
    function getName();

    /**
     * @param string $email
     */
    function setEmail($email);

    /**
     * @param string $name
     */
    function setName($name);

    /**
     * @return string
     */
    function getEmail();
}