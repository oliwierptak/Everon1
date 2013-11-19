<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\View;

use Everon\Helper;
use Everon\Interfaces;


abstract class Element extends Helper\Popo implements Interfaces\ViewElement
{
    /**
     * @param array $defaults
     * @param mixed $data
     */
    public function __construct($defaults, $data=null)
    {
        $data = (!is_array($data)) ? [] : $data;
        $data = array_merge($defaults, $data);

        parent::__construct($data);
    }

    /**
     * @param $name
     * @param $data
     */
    public function set($name, $data)
    {
        $this->data[$name] = $data;
    }

    /**
     * @param $name
     * @return null
     */
    public function get($name)
    {
        if (isset($this->data[$name]) === false) {
            return null;
        }
        
        return $this->data[$name];
    }
}