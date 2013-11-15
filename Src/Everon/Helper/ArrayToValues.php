<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Helper;


trait ArrayToValues
{

    public function arrayToValues($value)
    {
        $value = ($value instanceof \Closure) ? $value() : $value;
        return (is_object($value) && method_exists($value, 'toArray')) ? $value->toArray() : $value;
    }

}
