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
use Everon\Event\Dependency;


/**
 * @author Zeger Hoogeboom <zeger_hoogeboom@hotmail.com>
 * @author Oliwier Ptak <oliwierptak@gmail.com>
 */
class Context implements Interfaces\Context
{
    use Dependency\Injection\EventManager;
    
    /**
     * @var \Closure
     */
    protected $Callback = null;
    
    
    public function __construct(\Closure $Callback, $Scope)
    {
        $this->Callback = $Callback->bindTo($Scope);
    }
    
    public function __invoke()
    {
        return $this->Callback->__invoke();
    }

    /**
     * @param \Closure $Callback
     */
    public function setCallback(\Closure $Callback)
    {
        $this->Callback = $Callback;
    }

    /**
     * @return \Closure
     */
    public function getCallback()
    {
        return $this->Callback;
    }
} 