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
     * array|stdClass $this->data is declared in class which uses this trait
     *
     * @return array
     */
    public function isIterable($input)
    {
        if (is_array($input)) {
            return true;
        }
        
        if ($input instanceof \ArrayAccess || $input instanceof \Iterator) {
            return true;
        }
        
        return false;
    }

}
