<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Interfaces\Helper;

interface GetUrl 
{
    /**
     * Requires ConfigManager dependency
     * 
     * @param $name
     * @param array $query
     * @param array $get
     * @return string
     */
    function getUrl($name, $query=[], $get=[]);
}