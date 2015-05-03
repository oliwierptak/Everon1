<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Config\Interfaces;

interface Item extends \Everon\Interfaces\Arrayable
{
    /**
     * @return string
     */
    function getName();

    /**
     * @param $name
     */
    function setName($name);

    /**
     * @return bool
     */
    function isDefault();

    /**
     * @param bool $is_default
     */
    function setIsDefault($is_default);

    /**
     * @param $name
     * @param null $default
     * @return null
     */
    function getValueByName($name, $default=null);

    /**
     * @param $name
     * @param $value
     */
    function setValueByName($name, $value);

    /**
     * @param array $data
     * @throws \Everon\Exception\ConfigItem
     */
    function validateData(array $data);
}