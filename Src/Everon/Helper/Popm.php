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

use Everon\Exception;
use Everon\Interfaces;
use Everon\Helper;

/**
 * Plain Old PHP Object
 *
 * http://en.wikipedia.org/wiki/POJO
 */
class Popm extends Popo
{
    public function __get($property)
    {
        if (array_key_exists($property, $this->data) === false) {
            throw new Exception\Popo('Unknown public property: "%s" in "%s"', array($property, get_class($this)));
        }
        
        return $this->data[$property];
    }

    public function __set($name, $value)
    {
        $camelized = preg_split('/(?<=\\w)(?=[A-Z])/', $name);
        array_shift($camelized);
        $property = strtolower(implode('_', $camelized));
        
        $this->data[$property] = $value;
    }    
    
    public function __call($name, $arguments)
    {
        throw new Exception\Popo('Only public properties are available');
    }
        
}
