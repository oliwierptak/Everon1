<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Interfaces\Criteria;

interface Operator //extends \Everon\Interfaces\Arrayable, \Everon\Interfaces\Stringable 
{
    /**
     * @return string
     */
    function getType();

    /**
     * @param string $type
     */
    function setType($type);
        
    /**
     * @return string
     */
    function getTypeAsSql();

    /**
     * @param Criterium $Criterium
     * @return array
     */
    function toSqlPartData(Criterium $Criterium);
}