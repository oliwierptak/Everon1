<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Interfaces;


interface Config
{
    function getName();
    function setName($name);
    function getFilename();
    function setFilename($filename);
    function get($name, $default=null);
    /**
     * @param $where
     * @return \Everon\Interfaces\Config
     */    
    function go($where);
    function toArray();
}
