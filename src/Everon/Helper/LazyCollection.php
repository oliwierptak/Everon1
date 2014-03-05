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

use Everon\Helper;
use Everon\Interfaces;


class LazyCollection extends Collection
{
    /**
     * @var \Closure
     */
    protected $LazyDataLoader = null;
    
    
    public function __construct(\Closure $LazyDataLoader)
    {
        parent::__construct([]);
        $this->data = null;
        $this->LazyDataLoader = $LazyDataLoader;
    }
    
    protected function actuate()
    {
        if ($this->data === null) {
            $this->data = $this->LazyDataLoader->__invoke() ?: [];
        }
    }
    
    public function count()
    {
        $this->actuate();
        return parent::count();
    }

    public function offsetExists($offset)
    {
        $this->actuate();
        return parent::offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        $this->actuate();
        return parent::offsetGet($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->actuate();
        parent::offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->actuate();
        parent::offsetUnset($offset);
    }

    public function getIterator()
    {
        $this->actuate();
        return parent::getIterator();
    }

    public function has($name)
    {
        $this->actuate();
        return parent::has($name);
    }

    public function remove($name)
    {
        $this->actuate();
        parent::remove($name);
    }

    public function set($name, $value)
    {
        $this->actuate();
        parent::set($name, $value);
    }

    public function get($name, $default=null)
    {
        $this->actuate();
        return parent::get($name, $default);
    }
    
    public function toArray($deep=false)
    {
        $this->actuate();
        return parent::toArray($deep);
    }
    
}