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
    
    const WHEN_AFTER = 'after';
    const WHEN_BEFORE = 'before';

    const PROPAGATION_RUNNING = 1;
    const PROPAGATION_HALTED = 2;
    
    /**
     * @var \Everon\Interfaces\Collection
     */
    protected $Listeners = null;

    /**
     * @var int
     */
    protected $propagation;


    public function __construct()
    {
        $this->propagation = static::PROPAGATION_HALTED;
        $this->Listeners = new Helper\Collection([
            static::WHEN_BEFORE => [],
            static::WHEN_AFTER => []
        ]);
    }

    /**
     * @inheritdoc
     */
    public function dispatchBefore($event_name)
    {
        $this->propagation = static::PROPAGATION_RUNNING;
        return $this->dispatch($event_name, static::WHEN_BEFORE);
    }

    /**
     * @inheritdoc
     */
    public function dispatchAfter($event_name)
    {
        $this->propagation = static::PROPAGATION_RUNNING;
        return $this->dispatch($event_name, static::WHEN_AFTER);
    }

    /**
     * @param $event_name
     * @param $when
     * @return bool
     */
    protected function dispatch($event_name, $when)
    {
        if ($this->Listeners->get($when)->get($event_name) === null) {
            return null;
        }
        
        arsort($this->listeners[$event_name][$when], SORT_NUMERIC);
        
        $result = null;
        $this->propagation = static::PROPAGATION_RUNNING;
        
        foreach ($this->listeners[$event_name][$when] as $callbacks) {
            if ($this->propagation === static::PROPAGATION_HALTED) {
                break;
            }
            
            foreach ($callbacks as $priority => $Callback) {
                if (is_callable($Callback)) {
                    $result = $Callback();
                    if ($result === false) {
                        $this->propagation = static::PROPAGATION_HALTED;
                        break;
                    }
                }
            }
        }
        
        return $result !== false;
    }

    /**
     * @param $event_name
     * @param callable $Callback
     * @param int $priority
     */
    public function registerBefore($event_name, \Closure $Callback, $priority=1)
    {
        $this->register($event_name, $Callback, null, $priority);
    }

    /**
     * @param $event_name
     * @param callable $Callback
     * @param int $priority
     */
    public function registerAfter($event_name, \Closure $Callback, $priority=1)
    {
        $this->register($event_name, null, $Callback, $priority);
    }
    
    /**
     * @param $event_name
     * @param \Closure $BeforeExecuteCallback
     * @param \Closure $AfterExecuteCallback
     * @param int $priority
     */
    protected function register($event_name, \Closure $BeforeExecuteCallback=null, \Closure $AfterExecuteCallback=null, $priority)
    {
        $priority = (int) $priority;
        $priority = $priority !== 0 ?: 1;
        
        $index = 0;
        sd($this->listeners[$event_name][static::WHEN_BEFORE][$priority][$index]);
        while (isset($this->listeners[$event_name][static::WHEN_BEFORE][$priority][$index])) {
            $index++;
        }
        s($index);
        
        $this->listeners[$event_name][static::WHEN_BEFORE][$priority][$index] = $BeforeExecuteCallback;
        $this->listeners[$event_name][static::WHEN_AFTER][$priority][$index] = $AfterExecuteCallback;
    }

} 