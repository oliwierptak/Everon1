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

interface Config extends Arrayable, Dependency\Factory
{
    /**
     * @param $name
     * @param array $data
     * @return \Everon\Config\Interfaces\Item
     */
    function buildItem($name, array $data);

    function getName();

    /**
     * @inheritdoc
     */
    function setName($name);
    
    function getFilename();

    /**
     * @param $filename
     */
    function setFilename($filename);
    
    /**
     * @param mixed $Default
     */
    function setDefaultItem($Default);

    /**
     * @return mixed
     * @throws \Everon\Exception\Config
     */
    function getDefaultItem();

    /**
     * @return \array
     */
    function getItems();

    /**
     * @param array $items
     */
    function setItems(array $items);

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
     * @return bool
     */
    function isEmpty();

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
     * @param $data
     * @return mixed
     */
    function recompile($data);
}
