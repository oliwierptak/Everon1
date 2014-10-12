<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Interfaces;

interface SqlPart 
{
    /**
     * @return array
     */
    function getParameters();

    /**
     * @param array $parameters
     */
    function setParameters($parameters);

    /**
     * @return string
     */
    function getSql();

    /**
     * @param string $sql
     */
    function setSql($sql);
}
