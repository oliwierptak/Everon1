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
use Everon\Helper;

/**
 * @author Zeger Hoogeboom <zeger_hoogeboom@hotmail.com>
 */
class Manager implements Interfaces\Manager
{
    /**
     * @var array
     */
    protected $listeners = array();

    /**
     * @var Propagation $propagation
     */
    protected $propagation;

    public function __construct()
    {
        $this->propagation = Propagation::Running;
    }

    /**
     * @inheritdoc
     */
    public function dispatchBeforeExecute($eventName)
    {
        $this->propagation = Propagation::Running;
        $this->sortByPriority($eventName);
        $this->dispatch($eventName, 'before');
    }

    /**
     * @inheritdoc
     */
    public function dispatchAfterExecute($eventName)
    {
        $this->dispatch($eventName, 'after');
    }

    /**
     * @inheritdoc
     */
    public function dispatch($eventName, $when)
    {
        foreach ($this->listeners[$eventName][$when] as $listener) {
            if ($this->propagation === Propagation::Halted) {
                break;
            }
            if (is_callable($listener)) {
                $result = call_user_func($listener);
                if ($result === false) {
                    $this->propagation = Propagation::Halted;
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function register($eventName, $beforeExecuteCallback = null, $afterExecuteCallback = null, $priority = 0)
    {
        $index = 0;
        while(isset($this->listeners[$eventName]['before'][$priority][$index])) {
            $index++;
        }
        $this->listeners[$eventName]['before'][$priority][$index] = $beforeExecuteCallback;
        $this->listeners[$eventName]['after'][$priority][$index] = $afterExecuteCallback;
    }

    protected function countListeners($eventName)
    {
        return count($this->listeners[$eventName]);
    }

    protected function sortByPriority($eventName)
    {
        sort($this->listeners[$eventName]['before'],SORT_NUMERIC);
        sort($this->listeners[$eventName]['after'],SORT_NUMERIC);
    }

} 