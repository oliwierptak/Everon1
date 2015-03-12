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

interface SqlPart extends \Everon\Interfaces\Arrayable 
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
     * @param $name
     * @param $value
     */
    function setParameterValue($name, $value);

    /**
     * @param $name
     * @return mixed
     */
    function getParameterValue($name);
    
    /**
     * @return string
     */
    function getSql();

    /**
     * @param string $sql
     */
    function setSql($sql);

}
