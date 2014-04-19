<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Zeger Hoogeboom <zeger_hoogeboom@hotmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Event;


use Everon\Event\Interfaces\Event;
use Everon\Event\Interfaces\Listener;
use Everon\Exception\InvalidListener;

class Dispatcher implements Interfaces\Dispatcher
{
    /**
     * @var array
     */
    private $listeners = array();

    /**
     * @param $name
     * @param $Event Event
     */
    public function dispatch($name, $Event)
    {
        if (array_key_exists($name,$this->listeners))
        {
            $this->doDispatch($this->getListeners($name), $name, $Event);
        }
    }

    /**
     * @param $listeners
     * @param $name
     * @param $Event
     */
    protected function doDispatch($listeners, $name, $Event)
    {
        foreach ($listeners as $listener)
        {
            call_user_func($listener, $Event, $name, $this);
        }
    }

    /**
     * @inheritdoc
     */
    public function getListeners($name = null)
    {
        $listeners = array();
        foreach ($this->listeners as $eventName => $event) {
            if ($eventName === $name)
            {
                $listeners[] = $event;
            }
        }
        return $this->listeners;
    }

    /**
     * @inheritdoc
     */
    public function addListener($name, Listener $listener)
    {
        $this->listeners[$name] = $listener;
    }

    /**
     * @inheritdoc
     */
    public function hasListeners()
    {
        return (bool) count($this->listeners) === 0;
    }

    /**
     * @inheritdoc
     */
    public function countListeners($name)
    {
        return (int) count($this->listeners[$name]);
    }

    /**
     * @inheritdoc
     */
    public function removeListener($name)
    {
        if (!isset($this->listeners[$name])) {
            throw new InvalidListener('This listener was not registered.');
        }
        unset($this->listeners[$name]);
    }
} 