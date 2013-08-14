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
class Popo implements Interfaces\Arrayable
{
    use Helper\ToArray;

    /**
     * @var array
     */
    protected $data = [];


    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Exception\Popo
     */
    public function __call($name, $arguments)
    {
        if (empty($this->data)) {
            throw new Exception\Popo('Empty data in: "%s"', get_class($this));
        }

        $getter = strtolower(substr($name, 0, 3)) == 'get';
        $setter = strtolower(substr($name, 0, 3)) == 'set';

        if (!$setter && !$getter) {
            throw new Exception\Popo('Unknown method: "%s" in "%s"', array($name, get_class($this)));
        }

        $camelized = preg_split('/(?<=\\w)(?=[A-Z])/', $name);
        array_shift($camelized);
        $property = strtolower(implode('_', $camelized));

        if (array_key_exists($property, $this->data) === false) {
            if ($getter) {
                throw new Exception\Popo('Unknown property: "%s" in "%s"', array($property, get_class($this)));
            }
        }

        if ($getter) {
            return $this->data[$property];
        }

        if ($setter) {
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
    
}
