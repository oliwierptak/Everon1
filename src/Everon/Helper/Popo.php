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
 * Plain Old PHP Object, data accessible only via method calls, eg. $Popo->getTitle(), $Popo->setTitle('title')
 *
 * http://en.wikipedia.org/wiki/POJO
 */
class Popo implements Interfaces\Arrayable
{
    use Helper\ToArray;
    
    const CALL_TYPE_GETTER = 1;
    const CALL_TYPE_SETTER = 2;
    const CALL_TYPE_METHOD = 3;
    
    protected $call_type = null;
    
    protected $call_property = null;
    
    protected $property_name_cache = [];


    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    
    public function __get($property)
    {
        throw new Exception\Popo('Public getters are disabled. Requested: "%s"', $property);
    }
    
    public function __set($property, $value)
    {
        throw new Exception\Popo('Public setters are disabled. Requested: "%s"', $property);
    }
    
    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Exception\Popo
     */
    public function __call($name, $arguments)
    {
        $this->call_type = null;
        $this->call_property = null;
        
        if (empty($this->data)) {
            throw new Exception\Popo('Empty data in: "%s"', get_called_class());
        }

        $getter = strpos($name, 'get') === 0;
        $setter = strpos($name, 'set') === 0;

        if ($getter) {
            $this->call_type = static::CALL_TYPE_GETTER;
        } 
        else if ($setter) {
            $this->call_type = static::CALL_TYPE_SETTER;
        }

        if ($setter === false && $getter === false) {
            throw new Exception\Popo('Unknown method: "%s" in "%s"', [$name, get_called_class()]);
        }

        if (array_key_exists($name, $this->property_name_cache)) {
            $property = $this->property_name_cache[$name];
        }
        else {
            $camelized = preg_split('/(?<=\\w)(?=[A-Z])/', $name);
            array_shift($camelized);
            $property = mb_strtolower(implode('_', $camelized));
        }

        $this->call_property = $property;

        if (array_key_exists($property, $this->data) === false) {
            if ($getter) {
                throw new Exception\Popo('Unknown property: "%s" in "%s"', array($property, get_called_class()));
            }
        }

        if ($getter) {
            return $this->data[$property];
        }
        else if ($setter) {
            $this->data[$property] = $arguments[0];
        }
        
        return true;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
        $this->property_name_cache = [];
    }

    public function __sleep()
    {
        //todo: test me xxx
        return ['data'];
    }

    public static function __set_state(array $array)
    {
        //todo: test me xxx
        return new static($array['data']);
    }
    
}