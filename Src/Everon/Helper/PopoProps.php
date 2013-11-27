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
 * Plain Old PHP Object, data accessible only via public properties, eg. $PopoProps->title, $PopoProps->title = 'title'
 *
 * http://en.wikipedia.org/wiki/POJO
 */
class PopoProps extends Popo
{
    /**
     * When set to true, accessing unset property will throw exception
     * 
     * @var bool
     */
    protected $strict = false;
    
    public function __get($property)
    {
        $camelized = preg_split('/(?<=\\w)(?=[A-Z])/', $property);
        $property = strtolower(implode('_', $camelized));        
        if (array_key_exists($property, $this->data) === false) {
            if ($this->strict) {
                throw new Exception\Popo('Unknown public property: "%s" in "%s"', array($property, get_class($this)));
            }
            
            $this->data[$property] = null;
        }
        
        return $this->data[$property];
    }

    public function __set($name, $value)
    {
        $camelized = preg_split('/(?<=\\w)(?=[A-Z])/', $name);
        $property = strtolower(implode('_', $camelized));
        
        $this->data[$property] = $value;
    }
    
    public function __call($name, $arguments)
    {
        throw new Exception\Popo('Call by method: "%s" is not enabled in: "%"', [$name, 'PopoProps']);
    }
        
}
