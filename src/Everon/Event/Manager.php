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

/**
 * @author Zeger Hoogeboom <zeger_hoogeboom@hotmail.com>
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
     * @param array $listeners
     */
    public function setEvents(array $listeners)
    {
        $this->events = $listeners;
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
    public function dispatchBefore($event_name)
    {
        return $this->dispatch($event_name, static::DISPATCH_BEFORE);
    }

    /**
     * @inheritdoc
     */
    public function dispatchAfter($event_name)
    {
        return $this->dispatch($event_name, static::DISPATCH_AFTER);
    }

    /**
     * @param $event_name
     * @param $dispatch_type
     * @return bool|null
     */
    protected function dispatch($event_name, $dispatch_type)
    {
        if (isset($this->events[$event_name][$dispatch_type]) === false) {
            return null;
        }
        
        arsort($this->events[$event_name][$dispatch_type], SORT_NUMERIC);
        
        $result = null;
        $this->run();
        
        foreach ($this->events[$event_name][$dispatch_type] as $callbacks) {
            if ($this->isHalted()) {
                break;
            }
            
            foreach ($callbacks as $priority => $Callback) {
                if (is_callable($Callback)) {
                    $result = $Callback();
                    if ($result === false) {
                        $this->halt();
                        break;
                    }
                }
            }
        }
        
        return $result !== false;
    }

    /**
     * @param $event_name
     * @param Interfaces\Context $Context
     * @param int $priority
     */
    public function registerBefore($event_name, Interfaces\Context $Context, $priority=1)
    {
        $this->register($event_name, $Context, null, $priority);
    }

    /**
     * @param $event_name
     * @param Interfaces\Context $Callback
     * @param int $priority
     */
    public function registerAfter($event_name, Interfaces\Context $Callback, $priority=1)
    {
        $this->register($event_name, null, $Callback, $priority);
    }
    
    /**
     * @param $event_name
     * @param Interfaces\Context $BeforeExecuteCallback
     * @param Interfaces\Context $AfterExecuteCallback
     * @param int $priority
     */
    protected function register($event_name, Interfaces\Context $BeforeExecuteCallback=null, Interfaces\Context $AfterExecuteCallback=null, $priority)
    {
        $priority = (int) $priority;
        $priority = $priority === 0 ?: 1;
        
        if (isset($this->events[$event_name]) === false) {
            $this->events = [$event_name => [
                static::DISPATCH_BEFORE => [],
                static::DISPATCH_AFTER => []
            ]];
        }
        
        while (array_key_exists($priority, $this->events[$event_name][static::DISPATCH_BEFORE])) {
            $priority++;
        }

        $this->events[$event_name][static::DISPATCH_BEFORE][$priority] = $BeforeExecuteCallback;
        $this->events[$event_name][static::DISPATCH_AFTER][$priority] = $AfterExecuteCallback;
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