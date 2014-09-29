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
     * @param Criterium $Criterium
     * @return string
     */
    function toSql(Criterium $Criterium);
}