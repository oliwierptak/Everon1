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
    use Helper\IsCallable;

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
    public function dispatchBeforeExecute($moduleName,$controllerName, $action)
    {
        $this->propagation = Propagation::Running;
        $this->dispatch($moduleName,$controllerName, $action);
    }

    /**
     * @inheritdoc
     */
    public function dispatchAfterExecute($moduleName,$controllerName, $action)
    {
        $this->dispatch($moduleName,$controllerName, $action);
    }

    /**
     * @inheritdoc
     */
    public function dispatch($moduleName, $controllerName, $action)
    {
        foreach ($this->listeners[$moduleName][$controllerName][$action] as $listener) {
            if ($this->propagation === Propagation::Halted) {
                break;
            }
            $result = call_user_func($listener);
            if ($result === false) {
                $this->propagation = Propagation::Halted;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function register($moduleName, $controllerName, $action, $beforeExecuteCallback, $afterExecuteCallback)
    {
        if (is_callable($beforeExecuteCallback) === false || is_callable($afterExecuteCallback) === false) {
            throw new \Everon\Exception\Helper("This is not a valid callable");
        }
        //@TODO: add the afterExecuteCallback to the array, somehow retrieve 'the next' index for it and pass some parameter to dispatch()
        $this->listeners[$moduleName][$controllerName][$action][] = $beforeExecuteCallback;
    }

} 