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
    
    public function init()
    {
        if ($this->data === null) {
            $this->data = $this->LazyDataLoader->__invoke();
            $this->data = $this->data ?: [];
        }
    }
    
    public function count()
    {
        $this->init();
        return parent::count();
    }

    public function offsetExists($offset)
    {
        $this->init();
        return parent::offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        $this->init();
        return parent::offsetGet($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->init();
        parent::offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->init();
        parent::offsetUnset($offset);
    }

    public function getIterator()
    {
        $this->init();
        return parent::getIterator();
    }

    public function has($name)
    {
        $this->init();
        return parent::has($name);
    }

    public function remove($name)
    {
        $this->init();
        parent::remove($name);
    }

    public function set($name, $value)
    {
        $this->init();
        parent::set($name, $value);
    }

    public function get($name, $default=null)
    {
        $this->init();
        return parent::get($name, $default);
    }
    
}