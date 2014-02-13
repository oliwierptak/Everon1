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
class PopoProps implements Interfaces\Arrayable
{
    use Helper\ToArray;
    
    /**
     * When set to true, accessing unset property will throw exception
     * 
     * @var bool
     */
    protected $strict = false;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = array_change_key_case($data, \CASE_LOWER); 
    }
    
    public function __get($property)
    {
        $property = mb_strtolower($property);
        if (array_key_exists($property, $this->data) === false) {
            if ($this->strict) {
                throw new Exception\Popo('Unknown public property: "%s" in "%s"', [$property, get_class($this)]);
            }

            return null;
        }

        return $this->data[$property];
    }

    public function __set($name, $value)
    {
        $this->data[mb_strtolower($name)] = $value;
    }
    
    public function __call($name, $arguments)
    {
        throw new Exception\Popo('Call by method: "%s" is not allowed in: "%"', [$name, 'PopoProps']);
    }
    
    public function __sleep()
    {
        //todo: test me xxx
        return ['data', 'strict'];
    }
    
    public static function __set_state(array $array) 
    {
        //todo: test me
        $S = new static($array['data']);
        $S->strict = (bool) $array['strict'];
        return $S;
    }
        
}
