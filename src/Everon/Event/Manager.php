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

use Everon\Helper;
use Everon\Interfaces\Controller;

/**
 * @author Zeger Hoogeboom <zeger_hoogeboom@hotmail.com>
 * @author Oliwier Ptak <oliwierptak@gmail.com>
 */
class Manager implements Interfaces\Manager
{
    use Helper\Asserts\IsArrayKey;
    use Helper\Exceptions;
    
    const DISPATCH_AFTER = 'after';
    const DISPATCH_BEFORE = 'before';
    
    const PROPAGATION_RUNNING = 'running';
    const PROPAGATION_HALTED = 'halted';
    
    /**
     * @var array
     */
    protected $events = [];

    /**
     * @var int
     */
    protected $propagation;   


    public function __construct()
    {
        $this->propagation = static::PROPAGATION_HALTED;
    }

    /**
     * @param array $events
     */
    public function setEvents(array $events)
    {
        $this->events = $events;
    }

    /**
     * @return array
     */
    public function getEvents()
    {
        return $this->events;
    }
    
    /**
     * @inheritdoc
     */
    public function dispatchBefore($event_name, Controller $Controller)
    {
        return $this->dispatch($event_name, static::DISPATCH_BEFORE, $Controller);
    }

    /**
     * @inheritdoc
     */
    public function dispatchAfter($event_name, Controller $Controller)
    {
        return $this->dispatch($event_name, static::DISPATCH_AFTER, $Controller);
    }

    /**
     * @inheritdoc
     */
    public function registerBefore($event_name, Interfaces\Context $Context, $priority=1)
    {
        $this->register($event_name, $Context, null, $priority);
    }

    /**
     * @inheritdoc
     */
    public function registerAfter($event_name, Interfaces\Context $Context, $priority=1)
    {
        $this->register($event_name, null, $Context, $priority);
    }

    /**
     * @param $event_name
     * @param $dispatch_type
     * @param Controller $Controller
     * @return bool|null
     */
    protected function dispatch($event_name, $dispatch_type, Controller $Controller)
    {
        if (isset($this->events[$event_name][$dispatch_type]) === false) {
            return null;
        }

        uksort($this->events[$event_name][$dispatch_type], function ($a, $b) {
            return (int) $a < (int) $b; //reverse order
        });

        $this->run();
        $result = null;

        /**
         * @var Interfaces\Context $Context
         */
        foreach ($this->events[$event_name][$dispatch_type] as $Context) {
            if ($this->isHalted()) {
                break;
            }

            if ($Context instanceof Interfaces\Context) {
                $Context->setCaller($Controller);
                $result = $Context->execute();
                if ($result === false) {
                    $this->halt();
                    break;
                }
            }
        }

        return $result !== false;
    }

    /**
     * @param $event_name
     * @param Interfaces\Context $BeforeExecuteContext
     * @param Interfaces\Context $AfterExecuteContext
     * @param int $priority
     */
    protected function register($event_name, Interfaces\Context $BeforeExecuteContext=null, Interfaces\Context $AfterExecuteContext=null, $priority)
    {
        $priority = (int) $priority;
        $priority = $priority === 0 ?: 1;
        
        if (isset($this->events[$event_name]) === false) {
            $this->events[$event_name] = [
                static::DISPATCH_BEFORE => [],
                static::DISPATCH_AFTER => []
            ];
        }
        
        while (array_key_exists($priority, $this->events[$event_name][static::DISPATCH_BEFORE])) {
            $priority++;
        }

        $this->events[$event_name][static::DISPATCH_BEFORE][$priority] = $BeforeExecuteContext;
        $this->events[$event_name][static::DISPATCH_AFTER][$priority] = $AfterExecuteContext;
    }
    
    protected function run()
    {
        $this->propagation = static::PROPAGATION_RUNNING;
    }

    protected function halt()
    {
        $this->propagation = static::PROPAGATION_HALTED;
    }
    
    protected function isHalted()
    {
        return $this->propagation === static::PROPAGATION_HALTED;
    }

} 