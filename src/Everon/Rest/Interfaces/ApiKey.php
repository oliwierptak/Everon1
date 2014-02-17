<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Rest\Interfaces;

interface ApiKey
{
    /**
     * @param string $secret
     */
    function setSecret($secret);

    /**
     * @return string
     */
    function getSecret();

    /**
     * @param string $id
     */
    function setId($id);

    /**
     * @return string
     */
    function getId();
}