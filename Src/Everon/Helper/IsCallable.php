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


trait IsCallable
{
    /**
     * @param mixed $Object
     * @param $method
     * @return bool
     */
    public function isCallable($Object, $method)
    {
        //method exists is faster, use it first
        if (method_exists($Object, $method) === false || is_callable([$Object, $method]) === false) {
            return false;
        }
        
        return true;
    }

}
