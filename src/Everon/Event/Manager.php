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
    
    const WHEN_AFTER = 'after';
    const WHEN_BEFORE = 'before';

    const PROPAGATION_RUNNING = 1;
    const PROPAGATION_HALTED = 2;
    
    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var int
     */
    protected $propagation;

    /**
     * @var \Everon\Interfaces\Collection
     */
    protected $Listeners = null;
    

    public function __construct()
    {
        $this->propagation = static::PROPAGATION_HALTED;
        $this->Listeners = new Helper\Collection([]);
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
        $this->assertIsArrayKey($event_name, $this->Listeners, 'Invalid event: "%s"');
        $this->assertIsArrayKey($when, $this->Listeners->get($event_name), 'Invalid event type: "%s"');
            
        $this->sortByPriority($event_name, $when);
        
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
    public function registerBefore($event_name, \Closure $Callback, $priority=0)
    {
        $this->register($event_name, $Callback, null, $priority);
    }

    /**
     * @param $event_name
     * @param callable $Callback
     * @param int $priority
     */
    public function registerAfter($event_name, \Closure $Callback, $priority=0)
    {
        $this->register($event_name, null, $Callback, $priority);
    }
    
    /**
     * @param $event_name
     * @param \Closure $BeforeExecuteCallback
     * @param \Closure $AfterExecuteCallback
     * @param int $priority
     */
    protected function register($event_name, \Closure $BeforeExecuteCallback=null, \Closure $AfterExecuteCallback=null, $priority=0)
    {
        $index = count(@$this->listeners[$event_name][$priority]);
        while (isset($this->listeners[$event_name][static::WHEN_BEFORE][$priority][$index])) {
            $index++;
        }
        
        $this->listeners[$event_name][static::WHEN_BEFORE][$priority][$index] = $BeforeExecuteCallback;
        $this->listeners[$event_name][static::WHEN_AFTER][$priority][$index] = $AfterExecuteCallback;
        
        $this->Listeners->get($event_name, new Helper\Collection([static::WHEN_BEFORE=>[], static::WHEN_AFTER=>[]]))
            ->get(static::WHEN_BEFORE, new Helper\Collection([$priority=>[]]))
            ->get($priority)->set($index, $BeforeExecuteCallback);
        
        $this->Listeners->get($event_name)->get(static::WHEN_AFTER)->get($priority)->set($index, $AfterExecuteCallback);
    }

    protected function sortByPriority($event_name, $when)
    {
        arsort($this->listeners[$event_name][$when], SORT_NUMERIC);
        arsort($this->Listeners->get($event_name)->get($when), SORT_NUMERIC);
    }

} 