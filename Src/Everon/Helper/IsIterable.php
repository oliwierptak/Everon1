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

trait IsIterable
{
    /**
     * @param $input
     * @return bool
     */
    protected function isIterable($input)
    {
        if (isset($input) && is_array($input)) {//isset is much faster
            return true;
        }
        
        if ($input instanceof Interfaces\Arrayable || $input instanceof \ArrayAccess || $input instanceof \Iterator) {
            return true;
        }
        
        return false;
    }

}
