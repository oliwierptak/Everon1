<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Interfaces\Dependency;

interface GetUrl
{
    /**
     * Requires ConfigManager dependency
     * 
     * @param $name
     * @param array $query
     * @param array $get
     * @return string
     * @throws \Everon\Exception\Application
     */
    function getUrl($name, $query=[], $get=[]);
}