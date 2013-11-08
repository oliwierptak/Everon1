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

use Everon\Interfaces;

interface Config
{
    function getName();
    function setName($name);
    function getFilename();
    function setFilename($filename);
    
    /**
     * @param mixed $Default
     */
    function setDefaultItem($Default);

    /**
     * @return mixed
     */
    function getDefaultItem();

    /**
     * @return \array
     */
    function getItems();

    /**
     * @param string $name
     * @return \Everon\Config\Item\Router
     */
    function getItemByName($name);

    /**
     * @param $name
     * @return bool
     */
    function itemExists($name);    

    /**
     * @param $name
     * @param null $default
     * @return mixed
     */
    function get($name, $default=null);
    
    /**
     * @param $where
     * @return \Everon\Interfaces\Config
     */    
    function go($where);

    /**
     * @return array
     */
    function toArray();
}
