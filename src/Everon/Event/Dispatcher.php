<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Event;

use Everon\Event\Interfaces\Listener;
use Everon\Exception\InvalidListener;

/**
 * @author Zeger Hoogeboom <zeger_hoogeboom@hotmail.com>
 */
class Dispatcher implements Interfaces\Dispatcher
{

    /**
     * @var array
     */
    private $listeners = array();

    /**
     * @inheritdoc
     */
    public function dispatch($controllerName)
    {
        $event_name = '';
        foreach ($this->listeners as $eventName => $listener) { //@TODO: looping through all listeners is gay..
            if ($listener === $controllerName) { //@TODO: The action name is appended in the register() function, make sure the matching goes alright, now it surely won't
                $event_name = $eventName;
                break;
            }
        }

        foreach ($this->listeners[$event_name] as $listener) {
            call_user_func($listener['callback']);
        }
    }
    /**
     * @inheritdoc
     */
    public function register($eventName, Listener $listener, $moduleName, $controllerName, $action, $callback)
    {
        if (array_key_exists($eventName,$this->listeners) == false ) {
            $this->listeners[$eventName] = 'Everon'.DIRECTORY_SEPARATOR.'Module'.DIRECTORY_SEPARATOR.$moduleName.DIRECTORY_SEPARATOR.$controllerName.DIRECTORY_SEPARATOR.$action.DIRECTORY_SEPARATOR;
        }
        $index = $this->countListeners($eventName) + 1;
        $this->listeners[$eventName][$index] = $listener;
        $this->listeners[$eventName][$index]['callback'] = $callback;
    }

    /**
     * @inheritdoc
     */
    public function getListeners($name = null)
    {
        $listeners = array();
        if ($this->listenerExists($name)) {
            foreach ($this->listeners[$name] as $listener) {
                $listeners[] = $listener;
            }
        }
        return $this->listeners;
    }

    /**
     * @inheritdoc
     */
    public function hasListeners()
    {
        return (bool) count($this->listeners) === 0;
    }

    public function listenerExists($name)
    {
        if (isset($this->listeners[$name])) {
            return true;
        }
        return false;
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
        if ($this->listenerExists($name) == false) {
            throw new InvalidListener('This listener was not registered.');
        }
        unset($this->listeners[$name]);
    }
} 